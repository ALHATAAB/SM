<?php

namespace Tests\Feature;

use App\Livewire\PaieRH\DetailEmployer;
use App\Livewire\PaieRH\EtatPaie;
use App\Livewire\PaieRH\Rubrique;
use App\Models\PaieRH\BulletinCalculerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\RubriqueModel;
use App\Services\CalculPaiePersonnelService;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Calcul automatique CNSS / ITS / FSSP à l'ajout de la rubrique sur le bulletin de l'employé.
 * (nécessite une base de test : DB_CONNECTION=sqlite DB_DATABASE=:memory:)
 */
class RubriquesAutoPaieTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clés étrangères actives : celles de la paie pointent maintenant vers les bonnes tables.
        DB::statement('PRAGMA foreign_keys = ON');
        $this->artisan('migrate:fresh');

        // Le menu/en-tête de l'application exigent un utilisateur connecté : sans intérêt ici.
        $dir = sys_get_temp_dir().'/paie_test_views';
        @mkdir($dir.'/components/layouts', 0777, true);
        foreach (['header', 'sidebar', 'footer', 'loading'] as $partie) {
            file_put_contents("$dir/components/layouts/$partie.blade.php", '');
        }
        $this->app['view']->getFinder()->prependLocation($dir);
    }

    private function employe(int $charges = 2): EmployerModel
    {
        return EmployerModel::create([
            'matricule' => 'E001', 'nom' => 'Test', 'prenom' => 'Paie',
            'date_naissance' => '1990-01-01', 'lieu_naissance' => 'Niamey', 'sexe' => 'Masculin',
            'adresse' => 'Niamey', 'telephone' => '90000000', 'poste' => 'Comptable',
            'date_embauche' => '2020-01-01',
            'charge_familliale' => (string) $charges,
        ]);
    }

    private function rubrique(string $nom, string $type, string $sens = 'Gain'): RubriqueModel
    {
        return RubriqueModel::create([
            'code_rubrique' => (string) (RubriqueModel::max('id') + 100),
            'rubrique' => $nom, 'type_rubrique' => $type,
            'sens_rubrique' => $sens, 'categorie_rubrique' => 'Personnel',
        ]);
    }

    private function auto(string $code): RubriqueModel
    {
        return RubriqueModel::where('calcul_auto', $code)->firstOrFail();
    }

    private function attacher(EmployerModel $e, RubriqueModel $r, array $extra = []): void
    {
        ModelBulletinModel::create(['employer_id' => $e->id, 'rubrique_id' => $r->id] + $extra);
    }

    private function montant(EmployerModel $e, string $code): ?float
    {
        $l = ModelBulletinModel::where('employer_id', $e->id)->where('rubrique_id', $this->auto($code)->id)->first();

        return $l ? (float) $l->montant_retenu : null;
    }

    public function test_calcul_cnss_its_fssp_selon_les_infos_du_salarie(): void
    {
        $e = $this->employe(2);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        foreach (['CNSS', 'ITS', 'FSSP'] as $code) {
            $this->attacher($e, $this->auto($code), ['montant_retenu' => 0]);
        }

        app(CalculPaiePersonnelService::class)->synchroniserModele($e);

        // CNSS 5,25 % de 300 000
        $this->assertSame(15750.0, $this->montant($e, 'CNSS'));
        // base 284 250 - frais professionnels 13 % (36 952,5) = 247 297,5 -> base ITS 247 000 -> abattement 10 % (2 charges) = 222 300 -> barème
        $this->assertSame(28325.0, $this->montant($e, 'ITS'));
        // FSSP 1 % de (300 000 - 15 750 - 28 325) = 255 925
        $this->assertSame(2559.0, $this->montant($e, 'FSSP'));
    }

    public function test_its_change_avec_les_personnes_a_charge(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);
        $this->attacher($e, $this->auto('ITS'), ['montant_retenu' => 0]);

        $service = app(CalculPaiePersonnelService::class);
        $service->synchroniserModele($e);
        $sansCharge = $this->montant($e, 'ITS');

        $e->update(['charge_familliale' => '4']);
        $service->synchroniserModele($e->fresh());

        $this->assertGreaterThan($this->montant($e, 'ITS'), $sansCharge);
        $this->assertSame(15750.0, $this->montant($e, 'CNSS')); // la CNSS ne dépend pas des charges
    }

    public function test_seules_les_rubriques_presentes_sur_le_bulletin_sont_calculees(): void
    {
        $e = $this->employe(2);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);

        app(CalculPaiePersonnelService::class)->synchroniserModele($e);

        $this->assertSame(15750.0, $this->montant($e, 'CNSS'));
        $this->assertNull($this->montant($e, 'ITS'));
        $this->assertNull($this->montant($e, 'FSSP'));

        $lignes = ModelBulletinModel::with('rubrique')->where('employer_id', $e->id)->get();
        $calcul = app(CalculPaiePersonnelService::class)->calculer($e, $lignes);
        $this->assertCount(1, $calcul['lignes']);
        $this->assertSame(300000.0 - 15750.0, $calcul['net']);
    }

    public function test_un_nouveau_gain_met_a_jour_les_montants(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);

        $service = app(CalculPaiePersonnelService::class);
        $service->synchroniserModele($e);
        $this->assertSame(10500.0, $this->montant($e, 'CNSS'));

        $this->attacher($e, $this->rubrique('Prime', 'Brute I'), ['montant_gain' => 100000]);
        $service->synchroniserModele($e);
        $this->assertSame(15750.0, $this->montant($e, 'CNSS'));
    }

    public function test_apercu_avant_ajout(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);

        $this->assertSame(10500.0, app(CalculPaiePersonnelService::class)->apercu($e, $this->auto('CNSS')));
        $this->assertNull($this->montant($e, 'CNSS')); // rien n'est enregistré par l'aperçu
    }

    public function test_migration_de_rattachement_pour_les_employes_existants(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);

        $migration = require database_path('migrations/2026_09_29_160000_attach_auto_rubriques_to_existing_employees.php');
        $migration->up();
        $migration->up(); // idempotente

        $this->assertSame(6, ModelBulletinModel::where('employer_id', $e->id)->count()); // salaire + CNSS, ITS, FSSP + CNSS et ANPE patronales
    }

    public function test_ajout_depuis_la_fiche_employe_calcule_le_montant(): void
    {
        DB::table('annee_scolaire_models')->insert([
            'annee' => 2026, 'date_debut' => '2026-09-01', 'date_fin' => '2027-06-30',
            'etat' => 'actif', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $e = $this->employe(2);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);

        // Le composant est recréé à chaque action, comme le fait Livewire à chaque requête.
        $composant = function (array $props) use ($e) {
            $c = Livewire::new(DetailEmployer::class);
            $c->mount(encrypt($e->id));
            foreach ($props as $nom => $valeur) {
                $c->$nom = $valeur;
            }

            return $c;
        };

        // Sélection de la rubrique : montant estimé affiché, rien d'enregistré
        $c = $composant([]);
        $c->rubrique_id = $this->auto('CNSS')->id;
        $c->updatedRubriqueId($c->rubrique_id);
        $this->assertSame(15750.0, $c->apercu);
        $html = $c->render()->with($c->all())->with("errors", new \Illuminate\Support\ViewErrorBag)->render();
        $this->assertStringContainsString('Calculé automatiquement', $html);
        $this->assertStringContainsString('15 750', $html);
        $this->assertNull($this->montant($e, 'CNSS'));

        // Ajout : pas de montant à saisir, le calcul est fait
        foreach (['CNSS', 'ITS', 'FSSP'] as $code) {
            $composant(['rubrique_id' => $this->auto($code)->id])->addRubriqueModelBulletin();
        }

        $this->assertSame(15750.0, $this->montant($e, 'CNSS'));
        $this->assertSame(28325.0, $this->montant($e, 'ITS'));
        $this->assertSame(2559.0, $this->montant($e, 'FSSP'));

        // Ajout d'une prime : les montants suivent
        $prime = $this->rubrique('Prime', 'Brute I');
        $composant(['rubrique_id' => $prime->id, 'montant' => 100000])->addRubriqueModelBulletin();
        $this->assertSame(21000.0, $this->montant($e, 'CNSS'));
        $itsAvecPrime = $this->montant($e, 'ITS');

        // Retrait de la CNSS : elle disparait et l'ITS/FSSP sont recalculés sans elle
        $composant(['rubrique_id' => $this->auto('CNSS')->id])->retirerRubriqueModelBulletin();
        $this->assertNull($this->montant($e, 'CNSS'));
        $this->assertGreaterThan($itsAvecPrime, $this->montant($e, 'ITS'));

        // Modification des personnes à charge : l'abattement, donc l'ITS, change
        $avant = $this->montant($e, 'ITS');
        $composant([
            'nom' => 'Test', 'prenom' => 'Paie', 'sexe' => 'Masculin', 'date_naissance' => '1990-01-01',
            'lieu_naissance' => 'Niamey', 'telephone' => '90000000', 'poste' => 'Comptable',
            'date_embauche' => '2020-01-01', 'adresse' => 'Niamey', 'charge_familliale' => 5,
        ])->editerEmployer();
        $this->assertLessThan($avant, $this->montant($e, 'ITS'));
    }

    private function moisEtAnnee(): array
    {
        DB::table('annee_scolaire_models')->insert([
            'annee' => 2026, 'date_debut' => '2026-09-01', 'date_fin' => '2027-06-30',
            'etat' => 'actif', 'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach (['Septembre', 'Octobre', 'Novembre'] as $mois) {
            DB::table('mois_paie_models')->insert(['mois' => $mois, 'created_at' => now(), 'updated_at' => now()]);
        }

        return [DB::table('mois_paie_models')->pluck('id')->all(), DB::table('annee_scolaire_models')->value('id')];
    }

    private function employeParametre(string $matricule): EmployerModel
    {
        $e = $this->employe(2);
        $e->update(['matricule' => $matricule]);
        $this->attacher($e, RubriqueModel::firstOrCreate(
            ['rubrique' => 'Salaire de base'],
            ['code_rubrique' => '900', 'type_rubrique' => 'Brute I', 'sens_rubrique' => 'Gain', 'categorie_rubrique' => 'Personnel']
        ), ['montant_gain' => 300000]);
        foreach (['CNSS', 'ITS', 'FSSP'] as $code) {
            $this->attacher($e, $this->auto($code), ['montant_retenu' => 0]);
        }
        app(CalculPaiePersonnelService::class)->synchroniserModele($e);

        return $e;
    }

    public function test_etat_de_paie_reprend_les_montants_calcules(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employeParametre('E001');

        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0];
        $c->addCalculEtat();

        $fiche = FichePaieModel::where('employer_id', $e->id)->firstOrFail();
        $this->assertSame(300000.0, (float) $fiche->total_gain);
        $this->assertSame(15750.0 + 28325.0 + 2559.0, (float) $fiche->total_retenu);
        $this->assertSame(300000.0 - 46634.0, (float) $fiche->salaire_net);
        // CNSS patronale : 6,25 + 8,40 + 1,75 = 16,4 % de 300 000, hors du net ; ANPE patronale : 1 %, ligne à part
        $this->assertSame(49200.0, (float) $fiche->cnss_patronal);
        $this->assertSame(3000.0, (float) $fiche->anpe_patronal);

        // une seule ligne par rubrique automatique sur le bulletin du mois
        foreach (['CNSS', 'ITS', 'FSSP'] as $code) {
            $this->assertSame(1, BulletinCalculerModel::where('employer_id', $e->id)->where('rubrique_id', $this->auto($code)->id)->count());
        }

        // CNSS patronale : vraie ligne (base, taux, montant), à part des gains et des retenues
        $pat = BulletinCalculerModel::where('employer_id', $e->id)->where('rubrique_id', $this->auto('CNSS_PAT')->id)->get();
        $this->assertCount(1, $pat);
        $this->assertSame(300000.0, (float) $pat[0]->base);
        $this->assertSame(16.4, (float) $pat[0]->taux);
        $this->assertSame(49200.0, (float) $pat[0]->montant_patronal);
        $this->assertNull($pat[0]->montant_gain);
        $this->assertNull($pat[0]->montant_retenu);

        // ANPE patronale : ligne à part, hors gains, retenues et net
        $anpe = BulletinCalculerModel::where('employer_id', $e->id)->where('rubrique_id', $this->auto('ANPE_PAT')->id)->get();
        $this->assertCount(1, $anpe);
        $this->assertSame(300000.0, (float) $anpe[0]->base);
        $this->assertSame(1.0, (float) $anpe[0]->taux);
        $this->assertSame(3000.0, (float) $anpe[0]->montant_patronal);
        $this->assertNull($anpe[0]->montant_gain);
        $this->assertNull($anpe[0]->montant_retenu);

        // le net ne change pas : somme des retenues des lignes = total_retenu de la fiche
        $this->assertSame((float) $fiche->total_retenu, (float) BulletinCalculerModel::where('employer_id', $e->id)->sum('montant_retenu'));
    }

    public function test_mois_saute_n_ecrit_rien_pour_personne(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $a = $this->employeParametre('E001');

        // « a » a déjà été payé en Novembre ; « b » n'a aucune fiche
        FichePaieModel::create(['employer_id' => $a->id, 'reference' => 'X', 'total_gain' => 1, 'total_retenu' => 0,
            'salaire_net' => 1, 'statuts' => 'EN COURS', 'mois_id' => $mois[2], 'annee_id' => $annee]);
        $this->employeParametre('E002');
        $avant = FichePaieModel::count();

        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0]; // Septembre < Novembre
        $c->addCalculEtat();

        $this->assertSame($avant, FichePaieModel::count());
        $this->assertSame(0, BulletinCalculerModel::count());
    }

    public function test_rubrique_automatique_seul_le_libelle_est_modifiable(): void
    {
        $its = $this->auto('ITS');

        $c = Livewire::new(Rubrique::class);
        $c->rubrique_id = $its->id;
        $c->rubrique = 'Impôt ITS';
        $c->type_rubrique = 'Brute I';
        $c->sens_rubrique = 'Gain';
        $c->categorie_rubrique = 'Enseignant';
        $c->updateRubrique();

        $its->refresh();
        $this->assertSame('Impôt ITS', $its->rubrique);
        $this->assertSame('Retenu', $its->sens_rubrique);
        $this->assertSame('Cotisation', $its->type_rubrique);
        $this->assertSame('Personnel', $its->categorie_rubrique);
    }
    public function test_recalcul_d_un_mois_deja_calcule_ajoute_les_rubriques_manquantes(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(2);
        $salaire = RubriqueModel::create([
            'code_rubrique' => '900', 'rubrique' => 'Salaire de base', 'type_rubrique' => 'Brute I',
            'sens_rubrique' => 'Gain', 'categorie_rubrique' => 'Personnel',
        ]);
        $this->attacher($e, $salaire, ['montant_gain' => 300000]);

        // 1er calcul : seulement le salaire sur le bulletin modèle
        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0];
        $c->addCalculEtat();
        $this->assertSame(1, BulletinCalculerModel::where('employer_id', $e->id)->count());

        // CNSS/ITS/FSSP ajoutées ensuite, puis recalcul du même mois (fiche non payée)
        foreach (['CNSS', 'ITS', 'FSSP'] as $code) {
            $this->attacher($e, $this->auto($code), ['montant_retenu' => 0]);
        }
        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0];
        $c->addCalculEtat();

        $fiche = FichePaieModel::where('employer_id', $e->id)->firstOrFail();
        $this->assertSame(1, FichePaieModel::where('employer_id', $e->id)->count());
        $this->assertSame(6, BulletinCalculerModel::where('employer_id', $e->id)->count()); // salaire + CNSS + ITS + FSSP + CNSS patronale + ANPE patronale
        $this->assertSame(300000.0 - 46634.0, (float) $fiche->salaire_net);
        $this->assertSame(49200.0, (float) $fiche->cnss_patronal);
        $this->assertSame(3000.0, (float) $fiche->anpe_patronal);

        $etat = app(\App\Services\BulletinPaieService::class)->etatPersonnel($mois[0], $annee);
        $this->assertCount(1, $etat['gains']);
        $this->assertCount(3, $etat['retenues']);

        // Fiche payée : plus jamais recalculée
        $fiche->update(['statuts' => 'PAIE']);
        ModelBulletinModel::where('employer_id', $e->id)->where('rubrique_id', $this->auto('FSSP')->id)->delete();
        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0];
        $c->addCalculEtat();
        $this->assertSame(6, BulletinCalculerModel::where('employer_id', $e->id)->count()); // salaire + CNSS + ITS + FSSP + CNSS patronale + ANPE patronale
    }
}
