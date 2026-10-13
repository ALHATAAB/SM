<?php

namespace Tests\Feature;

use App\Livewire\PaieRH\DetailEmployer;
use App\Livewire\PaieRH\EtatPaie;
use App\Models\PaieRH\BulletinCalculerModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\RubriqueModel;
use App\Services\BulletinPaieService;
use App\Services\CalculPaiePersonnelService;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Options des rubriques du bulletin de paie du personnel :
 *  - CNSS / ITS / FSSP calculées automatiquement OU saisies à la main ;
 *  - répartition d'un gain en brut imposable / non imposable ;
 *  - rubrique permanente (tous les mois) OU « ce mois seulement ».
 * (nécessite une base de test : DB_CONNECTION=sqlite DB_DATABASE=:memory:)
 */
class RubriquesPaieOptionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys = ON');
        $this->artisan('migrate:fresh');

        $dir = sys_get_temp_dir().'/paie_test_views';
        @mkdir($dir.'/components/layouts', 0777, true);
        foreach (['header', 'sidebar', 'footer', 'loading'] as $partie) {
            file_put_contents("$dir/components/layouts/$partie.blade.php", '');
        }
        $this->app['view']->getFinder()->prependLocation($dir);
    }

    private function employe(int $charges = 0, string $matricule = 'E001'): EmployerModel
    {
        return EmployerModel::create([
            'matricule' => $matricule, 'nom' => 'Test', 'prenom' => 'Paie',
            'date_naissance' => '1990-01-01', 'lieu_naissance' => 'Niamey', 'sexe' => 'Masculin',
            'adresse' => 'Niamey', 'telephone' => '90000000', 'poste' => 'Comptable',
            'date_embauche' => '2020-01-01', 'charge_familliale' => (string) $charges,
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

    private function attacher(EmployerModel $e, RubriqueModel $r, array $extra = []): ModelBulletinModel
    {
        return ModelBulletinModel::create(['employer_id' => $e->id, 'rubrique_id' => $r->id] + $extra);
    }

    private function calculer(EmployerModel $e, ?int $mois = null, ?int $annee = null): array
    {
        $lignes = ModelBulletinModel::with('rubrique')->where('employer_id', $e->id)->get();

        return app(CalculPaiePersonnelService::class)->calculer($e, $lignes, [], 1.0, $mois, $annee);
    }

    private function ligne(array $calcul, RubriqueModel $r): ?array
    {
        return collect($calcul['lignes'])->firstWhere('rubrique_id', $r->id);
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

        return [DB::table('mois_paie_models')->pluck('id')->all(), (int) DB::table('annee_scolaire_models')->value('id')];
    }

    // ------------------------------------------------------------------ 1. Saisie manuelle

    public function test_cnss_saisie_a_la_main_remplace_le_calcul_et_alimente_l_its(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);
        $this->attacher($e, $this->auto('ITS'), ['montant_retenu' => 0]);

        $auto = $this->calculer($e);
        $this->assertSame(15750.0, $this->ligne($auto, $this->auto('CNSS'))['montant_retenu']);

        ModelBulletinModel::where('rubrique_id', $this->auto('CNSS')->id)
            ->update(['mode_calcul' => 'manuel', 'montant_retenu' => 10000]);

        $manuel = $this->calculer($e);
        $cnss   = $this->ligne($manuel, $this->auto('CNSS'));
        $this->assertSame(10000.0, $cnss['montant_retenu']);
        $this->assertTrue($cnss['manuel']);
        // l'ITS se calcule sur la CNSS saisie (base imposable plus élevée => ITS plus élevé)
        $this->assertGreaterThan(
            $this->ligne($auto, $this->auto('ITS'))['montant_retenu'],
            $this->ligne($manuel, $this->auto('ITS'))['montant_retenu']
        );
        $this->assertSame(300000.0 - 10000.0 - $this->ligne($manuel, $this->auto('ITS'))['montant_retenu'], $manuel['net']);
    }

    public function test_its_et_fssp_saisis_a_la_main(): void
    {
        $e = $this->employe(2);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);
        $this->attacher($e, $this->auto('ITS'), ['montant_retenu' => 20000, 'mode_calcul' => 'manuel']);
        $this->attacher($e, $this->auto('FSSP'), ['montant_retenu' => 1500, 'mode_calcul' => 'manuel']);

        $c = $this->calculer($e);

        $this->assertSame(20000.0, $this->ligne($c, $this->auto('ITS'))['montant_retenu']);
        $this->assertSame(1500.0, $this->ligne($c, $this->auto('FSSP'))['montant_retenu']);
        $this->assertSame(300000.0 - 15750.0 - 20000.0 - 1500.0, $c['net']);
    }

    public function test_la_synchronisation_n_ecrase_pas_un_montant_manuel(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 12345, 'mode_calcul' => 'manuel']);

        $service = app(CalculPaiePersonnelService::class);
        $service->synchroniserModele($e);
        $this->assertSame(12345.0, (float) ModelBulletinModel::where('rubrique_id', $this->auto('CNSS')->id)->value('montant_retenu'));

        // retour en automatique : le montant est recalculé
        ModelBulletinModel::where('rubrique_id', $this->auto('CNSS')->id)->update(['mode_calcul' => 'auto']);
        $service->synchroniserModele($e);
        $this->assertSame(15750.0, (float) ModelBulletinModel::where('rubrique_id', $this->auto('CNSS')->id)->value('montant_retenu'));
    }

    public function test_les_parts_patronales_restent_calculees_meme_si_la_cnss_est_manuelle(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 1000, 'mode_calcul' => 'manuel']);

        $c = $this->calculer($e);

        $this->assertSame(49200.0, $c['patronal']);
        $this->assertSame(3000.0, $c['anpe_patronal']);
    }

    // ------------------------------------------------------------------ 2. Brut imposable / non imposable

    public function test_repartition_du_brut_selon_le_type_de_la_rubrique(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->rubrique('Prime de transport', 'Brute NI'), ['montant_gain' => 30000]);

        $c = $this->calculer($e);

        $this->assertSame(200000.0, $c['brut_imposable']);
        $this->assertSame(30000.0, $c['brut_non_imposable']);
        $this->assertSame(230000.0, $c['gain']);
    }

    public function test_un_gain_peut_etre_reparti_entre_imposable_et_non_imposable(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->rubrique('Indemnité de logement', 'Brute I'), ['montant_gain' => 100000, 'montant_imposable' => 40000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);
        $this->attacher($e, $this->auto('ITS'), ['montant_retenu' => 0]);

        $c = $this->calculer($e);

        $this->assertSame(240000.0, $c['brut_imposable']);
        $this->assertSame(60000.0, $c['brut_non_imposable']);
        // la CNSS porte sur le brut total, l'ITS seulement sur la part imposable
        $this->assertSame(15750.0, $this->ligne($c, $this->auto('CNSS'))['montant_retenu']);

        $tout = $this->employe(0, 'E002');
        $this->attacher($tout, $this->rubrique('Salaire de base 2', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($tout, $this->rubrique('Indemnité 2', 'Brute I'), ['montant_gain' => 100000]);
        $this->attacher($tout, $this->auto('CNSS'), ['montant_retenu' => 0]);
        $this->attacher($tout, $this->auto('ITS'), ['montant_retenu' => 0]);
        $cTout = $this->calculer($tout);

        $this->assertLessThan(
            $this->ligne($cTout, $this->auto('ITS'))['montant_retenu'],
            $this->ligne($c, $this->auto('ITS'))['montant_retenu']
        );
    }

    public function test_une_part_imposable_ne_depasse_jamais_le_gain(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Prime', 'Brute NI'), ['montant_gain' => 50000, 'montant_imposable' => 20000]);

        $c = $this->calculer($e);

        $this->assertSame(20000.0, $c['brut_imposable']);
        $this->assertSame(30000.0, $c['brut_non_imposable']);
    }

    public function test_le_prorata_conserve_la_proportion_imposable(): void
    {
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Prime', 'Brute I'), ['montant_gain' => 100000, 'montant_imposable' => 25000]);
        $lignes = ModelBulletinModel::with('rubrique')->where('employer_id', $e->id)->get();

        $c = app(CalculPaiePersonnelService::class)->calculer($e, $lignes, [], 0.5);

        $this->assertSame(12500.0, $c['brut_imposable']);
        $this->assertSame(37500.0, $c['brut_non_imposable']);
    }

    public function test_l_etat_de_paie_enregistre_et_affiche_la_repartition(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->rubrique('Prime de transport', 'Brute NI'), ['montant_gain' => 30000]);
        $this->attacher($e, $this->rubrique('Logement', 'Brute I'), ['montant_gain' => 100000, 'montant_imposable' => 40000]);

        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0];
        $c->addCalculEtat();

        $fiche = FichePaieModel::where('employer_id', $e->id)->firstOrFail();
        $this->assertSame(240000.0, (float) $fiche->brut_imposable);
        $this->assertSame(90000.0, (float) $fiche->brut_non_imposable);
        $this->assertSame(330000.0, (float) $fiche->total_gain);

        $ligne = BulletinCalculerModel::where('employer_id', $e->id)->where('montant_gain', 100000)->firstOrFail();
        $this->assertSame(40000.0, (float) $ligne->montant_imposable);

        $bulletin = app(BulletinPaieService::class)->personnel($mois[0], $annee)[0];
        $this->assertSame(240000.0, $bulletin['brut_imposable']);
        $this->assertSame(90000.0, $bulletin['brut_non_imposable']);

        $etat = app(BulletinPaieService::class)->etatPersonnel($mois[0], $annee);
        $this->assertSame(240000.0, $etat['totaux']['imposable']);
        $this->assertSame(90000.0, $etat['totaux']['non_imposable']);
    }

    // ------------------------------------------------------------------ 3. Tout le temps / ce mois seulement

    public function test_une_rubrique_ce_mois_seulement_ne_compte_que_pour_ce_mois(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->rubrique('Prime exceptionnelle', 'Brute I'), [
            'montant_gain' => 50000, 'permanente' => false, 'mois_id' => $mois[1], 'annee_id' => $annee,
        ]);

        $this->assertSame(200000.0, $this->calculer($e, $mois[0], $annee)['gain']); // septembre
        $this->assertSame(250000.0, $this->calculer($e, $mois[1], $annee)['gain']); // octobre
        $this->assertSame(200000.0, $this->calculer($e, $mois[2], $annee)['gain']); // novembre
        $this->assertSame(200000.0, $this->calculer($e)['gain']);                   // aperçu : permanentes seulement
    }

    public function test_l_etat_de_paie_applique_les_rubriques_du_mois(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->rubrique('Prime exceptionnelle', 'Brute I'), [
            'montant_gain' => 50000, 'permanente' => false, 'mois_id' => $mois[1], 'annee_id' => $annee,
        ]);
        $this->attacher($e, $this->rubrique('Avance', 'Non soumise', 'Retenu'), ['montant_retenu' => 10000]);

        foreach ([0, 1, 2] as $i) {
            $c = Livewire::new(EtatPaie::class);
            $c->mois_paie = $mois[$i];
            $c->addCalculEtat();
        }

        $fiche = fn (int $i) => FichePaieModel::where('employer_id', $e->id)->where('mois_id', $mois[$i])->firstOrFail();
        $this->assertSame(200000.0, (float) $fiche(0)->total_gain);
        $this->assertSame(250000.0, (float) $fiche(1)->total_gain);
        $this->assertSame(200000.0, (float) $fiche(2)->total_gain);
        $this->assertSame(10000.0, (float) $fiche(1)->total_retenu); // la retenue permanente est reprise chaque mois

        $primeLignes = BulletinCalculerModel::where('employer_id', $e->id)->where('montant_gain', 50000)->get();
        $this->assertCount(1, $primeLignes);
        $this->assertSame($mois[1], (int) $primeLignes[0]->mois_id);
    }

    public function test_un_employe_sans_rubrique_applicable_ce_mois_n_a_pas_de_fiche(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Prime exceptionnelle', 'Brute I'), [
            'montant_gain' => 50000, 'permanente' => false, 'mois_id' => $mois[1], 'annee_id' => $annee,
        ]);

        $c = Livewire::new(EtatPaie::class);
        $c->mois_paie = $mois[0];
        $c->addCalculEtat();

        $this->assertSame(0, FichePaieModel::where('employer_id', $e->id)->count());
    }

    public function test_les_rubriques_automatiques_peuvent_etre_ponctuelles(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0, 'permanente' => false, 'mois_id' => $mois[0], 'annee_id' => $annee]);

        $this->assertSame(15750.0, $this->calculer($e, $mois[0], $annee)['retenu']);
        $this->assertSame(0.0, $this->calculer($e, $mois[1], $annee)['retenu']);
    }

    // ------------------------------------------------------------------ Fiche employé (écran)

    /** Le composant est recréé à chaque action, comme le fait Livewire à chaque requête. */
    private function composant(EmployerModel $e, array $props = []): DetailEmployer
    {
        $c = Livewire::new(DetailEmployer::class);
        $c->mount(encrypt($e->id));
        foreach ($props as $nom => $valeur) {
            $c->$nom = $valeur;
        }

        return $c;
    }

    /** Lance une action et retourne les champs refusés par la validation (liste vide si acceptée). */
    private function erreurs(callable $action): array
    {
        try {
            $action();
        } catch (\Illuminate\Validation\ValidationException $ex) {
            return array_keys($ex->errors());
        }

        return [];
    }

    public function test_ajout_d_une_rubrique_ce_mois_seulement_depuis_la_fiche(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $prime = $this->rubrique('Prime exceptionnelle', 'Brute I');

        $this->composant($e, ['rubrique_id' => $prime->id, 'montant' => 50000, 'periodicite' => 'mois', 'mois_application' => $mois[1]])
            ->addRubriqueModelBulletin();

        $ligne = ModelBulletinModel::where('employer_id', $e->id)->firstOrFail();
        $this->assertFalse((bool) $ligne->permanente);
        $this->assertSame($mois[1], (int) $ligne->mois_id);
        $this->assertSame($annee, (int) $ligne->annee_id);
        $this->assertSame(50000.0, (float) $ligne->montant_gain);
    }

    public function test_mois_obligatoire_quand_la_rubrique_est_ponctuelle(): void
    {
        $this->moisEtAnnee();
        $e = $this->employe(0);
        $prime = $this->rubrique('Prime', 'Brute I');

        $c = $this->composant($e, ['rubrique_id' => $prime->id, 'montant' => 1000, 'periodicite' => 'mois', 'mois_application' => null]);

        $this->assertSame(['mois_application'], $this->erreurs(fn () => $c->addRubriqueModelBulletin()));
        $this->assertSame(0, ModelBulletinModel::count());
    }

    public function test_pas_de_doublon_et_meme_rubrique_possible_sur_deux_mois_differents(): void
    {
        [$mois] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $prime = $this->rubrique('Prime', 'Brute I');

        $ajouter = fn (string $periodicite, ?int $m) => $this->composant($e, [
            'rubrique_id' => $prime->id, 'montant' => 1000, 'periodicite' => $periodicite, 'mois_application' => $m ?: now()->month,
        ])->addRubriqueModelBulletin();

        $ajouter('mois', $mois[0]);
        $ajouter('mois', $mois[1]);
        $this->assertSame(2, ModelBulletinModel::where('employer_id', $e->id)->count());

        $ajouter('mois', $mois[1]);   // même mois : refusé
        $this->assertSame(2, ModelBulletinModel::where('employer_id', $e->id)->count());

        // Une rubrique permanente puis la même en « ce mois seulement » : refusée (elle s'applique déjà tous les mois)
        $autre = $this->rubrique('Indemnité', 'Brute I');
        $this->composant($e, ['rubrique_id' => $autre->id, 'montant' => 500, 'periodicite' => 'permanente'])->addRubriqueModelBulletin();
        $this->composant($e, ['rubrique_id' => $autre->id, 'montant' => 500, 'periodicite' => 'mois', 'mois_application' => $mois[2]])->addRubriqueModelBulletin();
        $this->assertSame(1, ModelBulletinModel::where('rubrique_id', $autre->id)->count());
    }

    public function test_ajout_manuel_d_une_cnss_depuis_la_fiche(): void
    {
        $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 300000]);

        $this->composant($e, ['rubrique_id' => $this->auto('CNSS')->id, 'mode_calcul' => 'manuel', 'montant' => 9000])
            ->addRubriqueModelBulletin();

        $ligne = ModelBulletinModel::where('rubrique_id', $this->auto('CNSS')->id)->firstOrFail();
        $this->assertSame('manuel', $ligne->mode_calcul);
        $this->assertSame(9000.0, (float) $ligne->montant_retenu); // jamais écrasé par la synchronisation
    }

    public function test_cnss_manuelle_sans_montant_est_refusee(): void
    {
        $this->moisEtAnnee();
        $e = $this->employe(0);
        $c = $this->composant($e, ['rubrique_id' => $this->auto('CNSS')->id, 'mode_calcul' => 'manuel', 'montant' => null]);

        $this->assertSame(['montant'], $this->erreurs(fn () => $c->addRubriqueModelBulletin()));
        $this->assertSame(0, ModelBulletinModel::count());
    }

    public function test_ajout_d_un_gain_avec_part_imposable_et_validation(): void
    {
        $this->moisEtAnnee();
        $e = $this->employe(0);
        $logement = $this->rubrique('Logement', 'Brute I');

        $c = $this->composant($e, ['rubrique_id' => $logement->id, 'montant' => 100000, 'montant_imposable' => 150000]);
        $this->assertSame(['montant_imposable'], $this->erreurs(fn () => $c->addRubriqueModelBulletin())); // supérieur au montant
        $this->assertSame(0, ModelBulletinModel::count());

        $this->composant($e, ['rubrique_id' => $logement->id, 'montant' => 100000, 'montant_imposable' => 40000])->addRubriqueModelBulletin();
        $this->assertSame(40000.0, (float) ModelBulletinModel::firstOrFail()->montant_imposable);
    }

    public function test_modification_d_une_ligne_existante(): void
    {
        [$mois] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $prime = $this->attacher($e, $this->rubrique('Prime', 'Brute I'), ['montant_gain' => 50000]);
        $its = $this->attacher($e, $this->auto('ITS'), ['montant_retenu' => 0]);

        $c = $this->composant($e);
        $c->editLigne($prime->id);
        $c->montant = 60000; $c->montant_imposable = 10000; $c->periodicite = 'mois'; $c->mois_application = $mois[2];
        $c->updateLigne();

        $prime->refresh();
        $this->assertSame(60000.0, (float) $prime->montant_gain);
        $this->assertSame(10000.0, (float) $prime->montant_imposable);
        $this->assertFalse((bool) $prime->permanente);
        $this->assertSame($mois[2], (int) $prime->mois_id);

        $c = $this->composant($e);
        $c->editLigne($its->id);
        $c->mode_calcul = 'manuel'; $c->montant = 7777;
        $c->updateLigne();
        $this->assertSame('manuel', $its->fresh()->mode_calcul);
        $this->assertSame(7777.0, (float) $its->fresh()->montant_retenu);

        // retour au calcul automatique : le montant est recalculé
        $this->attacher($e, $this->auto('CNSS'), ['montant_retenu' => 0]);
        $c = $this->composant($e);
        $c->editLigne($its->id);
        $c->mode_calcul = 'auto';
        $c->updateLigne();
        $this->assertSame('auto', $its->fresh()->mode_calcul);
        $this->assertNotSame(7777.0, (float) $its->fresh()->montant_retenu);

        // une ligne d'un autre employé n'est pas modifiable
        $autre = $this->employe(0, 'E009');
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->composant($autre)->editLigne($prime->id);
    }

    public function test_retrait_d_une_ligne_precise(): void
    {
        [$mois] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $prime = $this->rubrique('Prime', 'Brute I');
        $a = $this->attacher($e, $prime, ['montant_gain' => 1000, 'permanente' => false, 'mois_id' => $mois[0], 'annee_id' => 1]);
        $b = $this->attacher($e, $prime, ['montant_gain' => 2000, 'permanente' => false, 'mois_id' => $mois[1], 'annee_id' => 1]);

        $this->composant($e, ['ligne_retrait' => $a->id])->retirerRubriqueModelBulletin();

        $this->assertNull(ModelBulletinModel::find($a->id));
        $this->assertNotNull(ModelBulletinModel::find($b->id));
    }

    public function test_la_fiche_separe_la_repartition_et_les_rubriques_ponctuelles(): void
    {
        [$mois, $annee] = $this->moisEtAnnee();
        $e = $this->employe(0);
        $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 200000]);
        $this->attacher($e, $this->rubrique('Transport', 'Brute NI'), ['montant_gain' => 30000]);
        $this->attacher($e, $this->rubrique('Prime exceptionnelle', 'Brute I'), ['montant_gain' => 50000, 'permanente' => false, 'mois_id' => $mois[1], 'annee_id' => $annee]);

        $c = $this->composant($e);

        $this->assertSame(200000.0, $c->repartition['imposable']);
        $this->assertSame(30000.0, $c->repartition['non_imposable']);
        $this->assertSame(230000.0, (float) $c->somme_gain); // la prime du mois n'est pas dans le bulletin « standard »
        $this->assertCount(1, $c->model_rubrique_ponctuelles);
        $this->assertSame('Prime exceptionnelle', $c->model_rubrique_ponctuelles[0]->rubrique->rubrique);
        $this->assertCount(2, $c->model_rubrique_gain);
    }

    public function test_les_lignes_existantes_restent_permanentes_et_automatiques(): void
    {
        $e = $this->employe(0);
        $l = $this->attacher($e, $this->rubrique('Salaire de base', 'Brute I'), ['montant_gain' => 1000])->fresh();

        $this->assertTrue((bool) $l->permanente);
        $this->assertSame('auto', $l->mode_calcul);
        $this->assertNull($l->montant_imposable);
    }
}
