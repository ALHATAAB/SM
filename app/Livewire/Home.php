<?php

namespace App\Livewire;

use App\Livewire\Comptabilite\EncaissementDivers;
use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\DepotBanqueModel;
use App\Models\Comptabilite\EncaissementDiversModel;
use App\Models\Comptabilite\OperationDepenseModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\HistoriqueAbonnementModel;
use App\Models\Scolarite\HistoriqueInscriptionModel;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\VenteModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class Home extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $caisse = [];
    public $countCaisse;
    public $montantInscrit, $fraiScolarite, $fraiScolaritePayer, $fraiScolariteRemise, $fraiScolariteImpayer;
    public $montantScolarite;
    public $countInscrit;
    public $countEnseignant;
    public $countPersonnel;
    public $montantEncaissement, $countEncaissement;
    public $inscriptionsParGroupes;
    public $inscriptionsParGroupesCount;
    public $gestionDepense, $totalDepense;
    public $gestionPaie, $totalPaie;
    public $gestionPaieEnseignant, $totalPaieEnseigant;
    public $Abonnement, $totalAbonnement;
    public $VenteLivre;
    public $SoldeTotalCaisse, $SoldeTotalBanque, $countSoldeTotalCaisse, $countSoldeTotalBanque;
    public $montantDepotBanque, $countDepotBanque;
    // Filtre des graphiques (année scolaire + période facultative)
    public $anneeFiltre;
    public $dateDebut = '';
    public $dateFin = '';
    public $repartitionPar = 'niveau'; // niveau | filiere | classe
    // Données des graphiques
    public array $statsMensuelles = [];
    public array $statsInscrits = [];
    public array $statsRecettes = [];
    public array $statsDepenses = [];
    public array $statsImpayes = [];
    public array $statsRepartition = [];
    public array $statsTotaux = ['recettes' => 0, 'depenses' => 0];

    /**
     * Vérifie si une colonne existe dans une table, avec cache statique
     * pour éviter de refaire la requête SHOW COLUMNS à chaque appel.
     */
    private static array $columnCache = [];

    private function hasColumn(string $table, string $column): bool
    {
        $key = $table . '.' . $column;

        if (!array_key_exists($key, self::$columnCache)) {
            try {
                self::$columnCache[$key] = Schema::hasColumn($table, $column);
            } catch (\Throwable $e) {
                Log::warning("Impossible de vérifier la colonne {$key}: " . $e->getMessage());
                self::$columnCache[$key] = false;
            }
        }

        return self::$columnCache[$key];
    }

    /**
     * Exécute une closure de manière sécurisée.
     * Si la requête échoue (colonne absente, table absente, etc.),
     * on log l'erreur et on retourne une valeur par défaut au lieu de planter.
     */
    private function safeQuery(\Closure $callback, $default = 0)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            Log::warning('Requête Home.php échouée: ' . $e->getMessage());
            return $default;
        }
    }

    public function updatingRecherche()
    {
        $this->resetPage();
    }

    // ------------------------------------------------------------------
    // Filtre des graphiques
    // ------------------------------------------------------------------
    public function updatedAnneeFiltre()
    {
        $this->majFiltre();
    }

    public function updatedDateDebut()
    {
        $this->majFiltre();
    }

    public function updatedDateFin()
    {
        $this->majFiltre();
    }

    public function updatedRepartitionPar()
    {
        $this->majFiltre();
    }

    public function reinitialiserFiltre()
    {
        $this->anneeFiltre = AnneeScolaire()->id ?? null;
        $this->dateDebut = '';
        $this->dateFin = '';
        $this->majFiltre();
    }

    private function majFiltre(): void
    {
        // Les propriétés publiques peuvent être modifiées depuis le navigateur :
        // on revérifie la permission et la validité de l'année.
        if (empty(Permission('Parametre'))) {
            return;
        }

        $existe = $this->safeQuery(
            fn() => AnneeScolaireModel::whereKey((int) $this->anneeFiltre)->exists(),
            false
        );
        if (!$existe) {
            $this->anneeFiltre = AnneeScolaire()->id ?? null;
        }

        $this->chargerStatistiques();
        $this->dispatch('stats-updated', stats: $this->donneesGraphiques());
    }

    private function donneesGraphiques(): array
    {
        return [
            'mensuel'  => $this->statsMensuelles,
            'inscrits' => $this->statsInscrits,
            'recettes' => $this->statsRecettes,
            'depenses' => $this->statsDepenses,
            'impayes'  => $this->statsImpayes,
            'repartition' => $this->statsRepartition,
            'totaux'   => $this->statsTotaux,
        ];
    }

    private function dateValide($valeur): ?Carbon
    {
        if (empty($valeur)) {
            return null;
        }
        try {
            return Carbon::parse($valeur)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ------------------------------------------------------------------
    // Requêtes statistiques (filtrées sur date_comptable)
    // ------------------------------------------------------------------
    private function requeteStats(string $model, array $filtres, bool $avecEtat, ?array $periode)
    {
        $query = $model::query();
        foreach ($filtres as $champ => $valeur) {
            $query->where($champ, $valeur);
        }
        if ($avecEtat && $this->hasColumn((new $model)->getTable(), 'etat')) {
            $query->where('etat', 1);
        }
        if ($periode) {
            $query->whereBetween('date_comptable', $periode);
        }
        return $query;
    }

    private function sommeStats(string $model, string $colonne, array $filtres, bool $avecEtat, ?array $periode): float
    {
        return (float) $this->safeQuery(
            fn() => $this->requeteStats($model, $filtres, $avecEtat, $periode)->sum($colonne),
            0
        );
    }

    private function sommeMensuelle(string $model, string $colonne, array $filtres, bool $avecEtat, ?array $periode): array
    {
        return $this->safeQuery(
            fn() => $this->requeteStats($model, $filtres, $avecEtat, $periode)
                ->selectRaw("DATE_FORMAT(date_comptable, '%Y-%m') as mois, SUM({$colonne}) as total")
                ->groupBy('mois')
                ->pluck('total', 'mois')
                ->all(),
            []
        );
    }

    /**
     * Données des graphiques pour l'année (et la période) du filtre.
     */
    private function chargerStatistiques(): void
    {
        $annee = $this->safeQuery(fn() => AnneeScolaireModel::find($this->anneeFiltre), null);

        if (!$annee) {
            $this->statsMensuelles = ['labels' => [], 'recettes' => [], 'depenses' => []];
            $this->statsInscrits   = ['items' => [], 'total' => 0];
            $this->statsRepartition = ['par' => 'niveau', 'labels' => [], 'data' => []];
            $this->statsRecettes   = ['labels' => [], 'data' => []];
            $this->statsDepenses   = ['labels' => [], 'data' => []];
            $this->statsImpayes    = ['labels' => [], 'data' => []];
            $this->statsTotaux     = ['recettes' => 0, 'depenses' => 0];
            return;
        }

        $id = (int) $annee->id;

        // Période facultative (sur date_comptable)
        $periode = null;
        $debut = $this->dateValide($this->dateDebut);
        $fin   = $this->dateValide($this->dateFin);
        if ($debut || $fin) {
            $debut = $debut ?? Carbon::parse($annee->date_debut ?? '1970-01-01');
            $fin   = $fin   ?? Carbon::parse($annee->date_fin ?? '2100-12-31');
            if ($debut->gt($fin)) {
                [$debut, $fin] = [$fin, $debut];
            }
            $periode = [$debut->toDateString(), $fin->toDateString()];
        }

        // Sources : clé de traduction => [modèle, colonne, filtres, filtrer sur etat]
        $sourcesRecettes = [
            'Solde_inscription'   => [HistoriqueInscriptionModel::class, 'frais_inscription', ['annee_scolaire_id' => $id], true],
            'Solde_scolarisation' => [HistoriquePaiementScolariteModel::class, 'frais_payer', ['annee_scolaire_id' => $id], true],
            'Abonnement'          => [HistoriqueAbonnementModel::class, 'frais_abonnement', ['annee_scolaire_id' => $id], true],
            'Total_vente_livre'   => [VenteModel::class, 'montant_total', ['annee_scolaire_id' => $id, 'statuts' => 'valider'], false],
            'Encaissement_divers' => [EncaissementDiversModel::class, 'montant', ['annee_scolaire_id' => $id, 'statuts' => 'AUTORISER'], false],
        ];
        $sourcesDepenses = [
            'Charge_divers'       => [OperationDepenseModel::class, 'montant', ['annee_scolaire_id' => $id], true],
            'Paiement_enseignant' => [HistoriquePaiementEnseignantModel::class, 'salaire_payer', ['annee_id' => $id], false],
            'Salaire_personnel'   => [HistoriquePaiementSalaireModel::class, 'salaire_payer', ['annee_id' => $id], false],
        ];

        $calculer = function (array $sources) use ($periode) {
            $totaux = [];
            $mensuel = [];
            foreach ($sources as $cle => [$model, $colonne, $filtres, $etat]) {
                $totaux[$cle]  = $this->sommeStats($model, $colonne, $filtres, $etat, $periode);
                $mensuel[$cle] = $this->sommeMensuelle($model, $colonne, $filtres, $etat, $periode);
            }
            return [$totaux, $mensuel];
        };

        [$totauxRecettes, $mensuelRecettes] = $calculer($sourcesRecettes);
        [$totauxDepenses, $mensuelDepenses] = $calculer($sourcesDepenses);

        // Mois affichés : ceux de la période (ou de l'année) + ceux qui ont des données
        $cles = [];
        $d0 = $d1 = null;
        if ($periode) {
            $d0 = Carbon::parse($periode[0])->startOfMonth();
            $d1 = Carbon::parse($periode[1])->startOfMonth();
        } elseif (!empty($annee->date_debut) && !empty($annee->date_fin)) {
            $d0 = Carbon::parse($annee->date_debut)->startOfMonth();
            $d1 = Carbon::parse($annee->date_fin)->startOfMonth();
        }
        if ($d0 && $d1) {
            for ($m = $d0->copy(), $n = 0; $m->lte($d1) && $n < 36; $m->addMonth(), $n++) {
                $cles[] = $m->format('Y-m');
            }
        }
        foreach ([$mensuelRecettes, $mensuelDepenses] as $groupe) {
            foreach ($groupe as $parMois) {
                $cles = array_merge($cles, array_keys($parMois));
            }
        }
        $cles = array_values(array_unique($cles));
        sort($cles);

        $locale = app()->getLocale();
        $labels = array_map(
            fn($k) => Carbon::createFromFormat('Y-m-d', $k . '-01')->locale($locale)->translatedFormat('M Y'),
            $cles
        );
        $serie = function (array $mensuel) use ($cles) {
            return array_map(function ($k) use ($mensuel) {
                $somme = 0.0;
                foreach ($mensuel as $parMois) {
                    $somme += (float) ($parMois[$k] ?? 0);
                }
                return $somme;
            }, $cles);
        };

        $this->statsMensuelles = [
            'labels'   => $labels,
            'recettes' => $serie($mensuelRecettes),
            'depenses' => $serie($mensuelDepenses),
        ];

        $traduire = fn(array $totaux) => [
            'labels' => array_map(fn($k) => __('message.' . $k), array_keys($totaux)),
            'data'   => array_values($totaux),
        ];
        $this->statsRecettes = $traduire($totauxRecettes);
        $this->statsDepenses = $traduire($totauxDepenses);
        $this->statsTotaux   = [
            'recettes' => array_sum($totauxRecettes),
            'depenses' => array_sum($totauxDepenses),
        ];

        // Inscrits par groupe et sexe : Hommes / Femmes / Garçons / Filles (année du filtre)
        $lignes = $this->safeQuery(function () use ($id) {
            $query = InscriptionModel::join('pre_inscription_models', 'inscription_models.pre_inscription_id', '=', 'pre_inscription_models.id')
                ->select('pre_inscription_models.groupe', 'pre_inscription_models.sexe', DB::raw('count(inscription_models.id) as total'))
                ->where('inscription_models.annee_scolaire_id', $id);
            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('inscription_models.etat', 1);
            }
            return $query->groupBy('pre_inscription_models.groupe', 'pre_inscription_models.sexe')->get();
        }, collect());

        $compte = ['hommes' => 0, 'femmes' => 0, 'garcons' => 0, 'filles' => 0, 'autres' => 0];
        foreach ($lignes as $ligne) {
            $groupe = mb_strtolower(trim((string) $ligne->groupe));
            $sexe   = mb_strtolower(trim((string) $ligne->sexe));
            $n      = (int) $ligne->total;

            if ($groupe === 'homme') {
                $compte['hommes'] += $n;
            } elseif ($groupe === 'femme') {
                $compte['femmes'] += $n;
            } elseif ($groupe === 'enfant') {
                // Les enfants sont séparés en garçons / filles selon le sexe
                if (str_starts_with($sexe, 'm')) {
                    $compte['garcons'] += $n;
                } elseif (str_starts_with($sexe, 'f')) {
                    $compte['filles'] += $n;
                } else {
                    $compte['autres'] += $n;
                }
            } else {
                $compte['autres'] += $n;
            }
        }

        $items = [];
        foreach ([
            ['hommes',  'Hommes',  '#ff6384'],
            ['femmes',  'Femmes',  '#36a2eb'],
            ['garcons', 'Garcons', '#2ecc71'],
            ['filles',  'Filles',  '#f5b041'],
        ] as [$cle, $libelle, $couleur]) {
            $items[] = ['cle' => $cle, 'label' => __('message.' . $libelle), 'total' => $compte[$cle], 'couleur' => $couleur];
        }
        if ($compte['autres'] > 0) {
            $items[] = ['cle' => 'autres', 'label' => __('message.Autres'), 'total' => $compte['autres'], 'couleur' => '#95a5a6'];
        }
        $this->statsInscrits = [
            'items' => $items,
            'total' => array_sum(array_column($items, 'total')),
        ];

        // Répartition des inscrits par niveau / filière / classe (liste blanche : pas d'injection)
        if (!in_array($this->repartitionPar, ['niveau', 'filiere', 'classe'], true)) {
            $this->repartitionPar = 'niveau';
        }
        [$tableRef, $colonneLibelle, $cleEtrangere] = [
            'niveau'  => ['niveau_etude_models', 'niveau', 'niveau_etude_id'],
            'filiere' => ['filiere_etude_models', 'filiere', 'filiere_etude_id'],
            'classe'  => ['classe_etude_models', 'classe', 'classe_etude_id'],
        ][$this->repartitionPar];

        $repartition = $this->safeQuery(function () use ($id, $tableRef, $colonneLibelle, $cleEtrangere) {
            $query = InscriptionModel::join($tableRef, "inscription_models.{$cleEtrangere}", '=', "{$tableRef}.id")
                ->where('inscription_models.annee_scolaire_id', $id);
            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('inscription_models.etat', 1);
            }
            return $query
                ->selectRaw("{$tableRef}.{$colonneLibelle} as libelle, COUNT(inscription_models.id) as total")
                ->groupBy("{$tableRef}.id", "{$tableRef}.{$colonneLibelle}")
                ->orderBy("{$tableRef}.id")
                ->get();
        }, collect());

        $this->statsRepartition = [
            'par'    => $this->repartitionPar,
            'labels' => $repartition->pluck('libelle')->all(),
            'data'   => $repartition->map(fn($r) => (int) $r->total)->all(),
        ];

        // Impayés de scolarité par niveau (même formule que la carte "Scolarité impayée")
        $impayes = $this->safeQuery(function () use ($id) {
            $query = InscriptionModel::join('niveau_etude_models', 'inscription_models.niveau_etude_id', '=', 'niveau_etude_models.id')
                ->where('inscription_models.annee_scolaire_id', $id);
            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('inscription_models.etat', 1);
            }
            return $query
                ->selectRaw('niveau_etude_models.niveau as niveau, SUM(inscription_models.frais_scolarite) - SUM(COALESCE(inscription_models.payer, 0)) - SUM(COALESCE(inscription_models.remise, 0)) as impaye')
                ->groupBy('niveau_etude_models.id', 'niveau_etude_models.niveau')
                ->orderBy('niveau_etude_models.id')
                ->get();
        }, collect());

        $this->statsImpayes = [
            'labels' => $impayes->pluck('niveau')->all(),
            'data'   => $impayes->map(fn($r) => max(0, (float) $r->impaye))->all(),
        ];
    }

    public function mount()
    {
        $annee = AnneeScolaire();
        $anneeId = $annee->id ?? null;

        if (!empty($anneeId)) {

            $this->montantInscrit = $this->safeQuery(function () use ($anneeId) {
                $query = HistoriqueInscriptionModel::where('annee_scolaire_id', $anneeId);
                if ($this->hasColumn('historique_inscription_models', 'etat')) {
                    $query->where('etat', 1);
                }
                return $query->sum('frais_inscription');
            });

            $this->countInscrit = $this->safeQuery(function () use ($anneeId) {
                $query = InscriptionModel::where('annee_scolaire_id', $anneeId);
                if ($this->hasColumn('inscription_models', 'etat')) {
                    $query->where('etat', 1);
                }
                return $query->count();
            });

            $this->montantEncaissement = $this->safeQuery(fn() =>
                EncaissementDiversModel::where('annee_scolaire_id', $anneeId)
                    ->where('statuts', 'AUTORISER')
                    ->sum('montant')
            );

            $this->countEncaissement = $this->safeQuery(fn() =>
                EncaissementDiversModel::where('annee_scolaire_id', $anneeId)
                    ->where('statuts', 'AUTORISER')
                    ->count()
            );

            $this->montantDepotBanque = $this->safeQuery(fn() =>
                DepotBanqueModel::where('annee_scolaire_id', $anneeId)
                    ->where('statuts', 'AUTORISER')
                    ->sum('montant')
            );

            $this->countDepotBanque = $this->safeQuery(fn() =>
                DepotBanqueModel::where('annee_scolaire_id', $anneeId)
                    ->where('statuts', 'AUTORISER')
                    ->count()
            );

            $this->countEnseignant = $this->safeQuery(fn() => EnseignantModel::count());
            $this->countPersonnel = $this->safeQuery(fn() => EmployerModel::count());

            $this->SoldeTotalCaisse = $this->safeQuery(fn() => CaisseModel::sum('solde'));
            $this->countSoldeTotalCaisse = $this->safeQuery(fn() => CaisseModel::count());

            $this->SoldeTotalBanque = $this->safeQuery(fn() => BanqueModel::sum('solde'));
            $this->countSoldeTotalBanque = $this->safeQuery(fn() => BanqueModel::count());

            $this->montantScolarite = $this->safeQuery(function () use ($anneeId) {
                $query = HistoriquePaiementScolariteModel::where('annee_scolaire_id', $anneeId);
                if ($this->hasColumn('historique_paiement_scolarite_models', 'etat')) {
                    $query->where('etat', 1);
                }
                return $query->sum('frais_payer');
            });

            $this->inscriptionsParGroupesCount = $this->safeQuery(function () use ($anneeId) {
                $query = InscriptionModel::join('pre_inscription_models', 'inscription_models.pre_inscription_id', '=', 'pre_inscription_models.id')
                    ->select('pre_inscription_models.groupe', DB::raw('count(inscription_models.id) as total'))
                    ->where('inscription_models.annee_scolaire_id', $anneeId);
                if ($this->hasColumn('inscription_models', 'etat')) {
                    $query->where('inscription_models.etat', 1);
                }
                return $query->groupBy('pre_inscription_models.groupe')->count();
            });

            $this->inscriptionsParGroupes = $this->safeQuery(function () use ($anneeId) {
                $query = InscriptionModel::join('pre_inscription_models', 'inscription_models.pre_inscription_id', '=', 'pre_inscription_models.id')
                    ->select('pre_inscription_models.groupe', DB::raw('count(inscription_models.id) as total'))
                    ->where('inscription_models.annee_scolaire_id', $anneeId);
                if ($this->hasColumn('inscription_models', 'etat')) {
                    $query->where('inscription_models.etat', 1);
                }
                return $query->groupBy('pre_inscription_models.groupe')->get();
            }, collect());

        } else {
            // Valeurs par défaut si aucune année scolaire active n'est trouvée
            $this->montantInscrit = 0;
            $this->countInscrit = 0;
            $this->montantEncaissement = 0;
            $this->countEncaissement = 0;
            $this->montantDepotBanque = 0;
            $this->countDepotBanque = 0;
            $this->countEnseignant = $this->safeQuery(fn() => EnseignantModel::count());
            $this->countPersonnel = $this->safeQuery(fn() => EmployerModel::count());
            $this->SoldeTotalCaisse = $this->safeQuery(fn() => CaisseModel::sum('solde'));
            $this->SoldeTotalBanque = $this->safeQuery(fn() => BanqueModel::sum('solde'));
            $this->montantScolarite = 0;
            $this->inscriptionsParGroupesCount = 0;
            $this->inscriptionsParGroupes = collect();
        }

        $this->gestionDepense = $this->safeQuery(function () use ($anneeId) {
            $query = OperationDepenseModel::where('annee_scolaire_id', $anneeId);
            if ($this->hasColumn('operation_depense_models', 'etat')) {
                $query->where('etat', 1);
            }
            return $query->sum('montant');
        });
        $this->totalDepense = $this->gestionDepense;

        $this->gestionPaie = $this->safeQuery(fn() =>
            HistoriquePaiementSalaireModel::where('annee_id', $anneeId)->sum('salaire_payer')
        );
        $this->totalPaie = $this->gestionPaie;

        $this->gestionPaieEnseignant = $this->safeQuery(fn() =>
            HistoriquePaiementEnseignantModel::where('annee_id', $anneeId)->sum('salaire_payer')
        );
        $this->totalPaieEnseigant = $this->gestionPaieEnseignant;

        $this->Abonnement = $this->safeQuery(function () use ($anneeId) {
            $query = HistoriqueAbonnementModel::where('annee_scolaire_id', $anneeId);
            if ($this->hasColumn('historique_abonnement_models', 'etat')) {
                $query->where('etat', 1);
            }
            return $query->sum('frais_abonnement');
        });
        $this->totalAbonnement = $this->Abonnement;

        $this->VenteLivre = $this->safeQuery(fn() =>
            VenteModel::where('annee_scolaire_id', $anneeId)->where('statuts', 'valider')->sum('montant_total')
        );

        $this->fraiScolarite = $this->safeQuery(function () use ($anneeId) {
            $query = InscriptionModel::where('annee_scolaire_id', $anneeId);
            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('etat', 1);
            }
            return $query->sum('frais_scolarite');
        });

        $this->fraiScolaritePayer = $this->safeQuery(function () use ($anneeId) {
            $query = InscriptionModel::where('annee_scolaire_id', $anneeId);
            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('etat', 1);
            }
            return $query->sum('payer');
        });

        $this->fraiScolariteRemise = $this->safeQuery(function () use ($anneeId) {
            $query = InscriptionModel::where('annee_scolaire_id', $anneeId);
            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('etat', 1);
            }
            return $query->sum('remise');
        });

        $this->fraiScolariteImpayer = $this->fraiScolarite - ($this->fraiScolaritePayer + $this->fraiScolariteRemise);

        // Les graphiques ne sont affichés (donc calculés) que pour le profil Parametre
        if (!empty(Permission('Parametre'))) {
            $this->anneeFiltre = $annee->id ?? null;
            $this->chargerStatistiques();
        }
    }

    public function render()
    {
        $anneeId = AnneeScolaire()->id ?? null;

        $situationInscrits = $this->safeQuery(function () use ($anneeId) {
            $query = InscriptionModel::query()
                ->where('annee_scolaire_id', $anneeId);

            if ($this->hasColumn('inscription_models', 'etat')) {
                $query->where('etat', 1);
            }

            if (!empty($this->recherche)) {
                $recherche = $this->recherche;

                $query->where(function ($q) use ($recherche) {
                    $q->where('matricule', 'like', "%$recherche%")
                        ->orWhereHas('preInscription', function ($sub) use ($recherche) {
                            $sub->where('nom', 'like', "%$recherche%")
                                ->orWhere('prenom', 'like', "%$recherche%")
                                ->orWhere('telephone', 'like', "%$recherche%");
                        })
                        ->orWhereHas('niveau', function ($sub) use ($recherche) {
                            $sub->where('niveau', 'like', "%$recherche%");
                        })
                        ->orWhereHas('filiere', function ($sub) use ($recherche) {
                            $sub->where('filiere', 'like', "%$recherche%");
                        });
                });
            }

            return $query->paginate(5);
        }, new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5));

        return view(
            'livewire.home',
            [
                'montantInscrit' => $this->montantInscrit,
                'countInscrit' => $this->countInscrit,
                'situationInscrits' => $situationInscrits,
                'anneesDisponibles' => $this->safeQuery(
                    fn() => AnneeScolaireModel::orderByDesc('annee')->get(),
                    collect()
                ),
                'donneesGraphiques' => $this->donneesGraphiques(),
            ]
        );
    }
}