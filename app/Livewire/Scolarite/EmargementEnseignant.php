<?php

namespace App\Livewire\Scolarite;

use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EmargementEnseignantModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\EtatPaieEnseignantModel;
use App\Services\DepartService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Émargement enseignant — saisie groupée.
 *
 * On choisit une date puis un enseignant (ou une classe) : toutes les séances
 * prévues ce jour-là d'après l'emploi du temps s'affichent. On renseigne le
 * statut de chacune (ou « Tous présents ») et UN SEUL enregistrement crée ou
 * met à jour tous les émargements du jour.
 */
class EmargementEnseignant extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /** Jours de la semaine, indexés par Carbon::dayOfWeekIso (1 = lundi). */
    protected const JOURS = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];

    // --- Saisie groupée ---
    public $mode = 'enseignant';      // 'enseignant' ou 'classe'
    public $date;
    public $enseignant_id;
    public $classe_id;
    public array $lignes = [];

    // --- Historique / filtres ---
    public $enseignantfilter;
    public $classefilter;
    public $dateDebut;
    public $dateFin;
    public array $selection = [];

    public function mount()
    {
        $this->date = now()->toDateString();
    }

    /* ------------------------------------------------------------------ */
    /*  Chargement des séances du jour                                     */
    /* ------------------------------------------------------------------ */

    public function updatedMode()
    {
        $this->reset(['enseignant_id', 'classe_id', 'lignes']);
        $this->resetErrorBag();
    }

    public function updatedDate()          { $this->chargerLignes(); }
    public function updatedEnseignantId()  { $this->chargerLignes(); }
    public function updatedClasseId()      { $this->chargerLignes(); }

    protected function jourFrancais(string $date): string
    {
        return self::JOURS[Carbon::parse($date)->dayOfWeekIso];
    }

    protected function chargerLignes(): void
    {
        $this->lignes = [];
        $this->resetErrorBag();

        $id = $this->mode === 'enseignant' ? $this->enseignant_id : $this->classe_id;
        if (!$this->date || !$id) {
            return;
        }

        $seances = SeanceModel::with(['enseignant', 'matiere', 'classe', 'salle'])
            ->where($this->mode . '_id', $id)
            ->where('jour', $this->jourFrancais($this->date))
            ->orderBy('heure_debut')
            ->get();

        // Enseignant parti avant cette date : on n'émarge plus
        $partis = $seances->filter(fn ($s) => DepartService::dateApresDepart($s->enseignant?->date_depart, $this->date));
        if ($partis->isNotEmpty()) {
            $seances = $seances->reject(fn ($s) => $partis->contains('id', $s->id))->values();

            $noms = $partis->map(fn ($s) => trim(($s->enseignant->nom ?? '') . ' ' . ($s->enseignant->prenom ?? '')))->unique()->implode(', ');
            session()->flash('warning', "Émargement impossible à cette date : $noms a quitté l'établissement (départ : "
                . Carbon::parse($partis->first()->enseignant->date_depart)->format('d/m/Y') . ').');
        }

        if ($seances->isEmpty()) {
            return;
        }

        // Émargements déjà saisis ce jour-là pour ces séances
        $existants = EmargementEnseignantModel::where('date', $this->date)
            ->whereIn('enseignant_id', $seances->pluck('enseignant_id')->unique())
            ->get();

        foreach ($seances as $s) {
            $e = $existants->first(fn ($m) =>
                $m->seance_id == $s->id
                // anciens émargements enregistrés sans seance_id
                || (is_null($m->seance_id)
                    && $m->enseignant_id == $s->enseignant_id
                    && $m->heure_arrivee == $s->heure_debut
                    && $m->heure_depart == $s->heure_fin));

            $this->lignes[] = [
                'seance_id' => $s->id,
                'emargement_id' => $e?->id,
                'enseignant' => trim(($s->enseignant->nom ?? '') . ' ' . ($s->enseignant->prenom ?? '')),
                'classe' => $s->classe->classe ?? '',
                'matiere' => $s->matiere->matiere_etude ?? '',
                'salle' => $s->salle->salle ?? '',
                'debut_prevu' => substr($s->heure_debut, 0, 5),
                'fin_prevue' => substr($s->heure_fin, 0, 5),
                'heure_arrivee' => substr($e->heure_arrivee ?? $s->heure_debut, 0, 5),
                'heure_depart' => substr($e->heure_depart ?? $s->heure_fin, 0, 5),
                'status' => $e->status ?? '',        // '' = pas encore émargé
            ];
        }
    }

    public function tousPresents()
    {
        foreach ($this->lignes as $i => $l) {
            $this->lignes[$i]['status'] = 'present';
        }
    }

    public function tousAbsents()
    {
        foreach ($this->lignes as $i => $l) {
            $this->lignes[$i]['status'] = 'absent';
        }
    }

    public function reinitialiserHoraires()
    {
        foreach ($this->lignes as $i => $l) {
            $this->lignes[$i]['heure_arrivee'] = $l['debut_prevu'];
            $this->lignes[$i]['heure_depart'] = $l['fin_prevue'];
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Enregistrement unique                                              */
    /* ------------------------------------------------------------------ */

    public function saveAll()
    {
        $anneeScolaire = AnneeScolaire();
        if (!$anneeScolaire) {
            session()->flash('danger', "Aucune année scolaire active. Veuillez en activer une avant d'enregistrer un émargement.");
            return;
        }

        $this->validate([
            'date' => 'required|date',
            'lignes.*.status' => 'nullable|in:present,absent',
            'lignes.*.heure_arrivee' => 'required|date_format:H:i',
            'lignes.*.heure_depart' => 'required|date_format:H:i',
        ], [
            'required' => 'Requis',
            'date_format' => 'Format HH:MM',
            'in' => 'Statut invalide',
        ]);

        // Seules les lignes dont le statut est renseigné sont enregistrées
        $aEnregistrer = array_filter($this->lignes, fn ($l) => in_array($l['status'], ['present', 'absent'], true));

        if (!$aEnregistrer) {
            session()->flash('warning', 'Renseignez au moins un statut (Présent ou Absent) avant d\'enregistrer.');
            return;
        }

        // On ne fait pas confiance aux ids envoyés par le navigateur : on
        // relit les séances en base.
        $seances = SeanceModel::with('enseignant')->whereIn('id', array_column($aEnregistrer, 'seance_id'))->get()->keyBy('id');
        $jour = $this->jourFrancais($this->date);

        $crees = $modifies = $ignores = 0;

        DB::transaction(function () use ($aEnregistrer, $seances, $anneeScolaire, $jour, &$crees, &$modifies, &$ignores) {
            foreach ($aEnregistrer as $l) {
                $seance = $seances[$l['seance_id']] ?? null;
                if (!$seance || $seance->jour !== $jour) {
                    continue;   // séance supprimée ou ne tombant pas ce jour-là
                }

                // Enseignant parti avant cette date : pas d'émargement
                if (DepartService::dateApresDepart($seance->enseignant?->date_depart, $this->date)) {
                    $ignores++;
                    continue;
                }

                // Un absent est enregistré sur l'horaire prévu
                $arrivee = $l['status'] === 'present' ? $l['heure_arrivee'] : substr($seance->heure_debut, 0, 5);
                $depart = $l['status'] === 'present' ? $l['heure_depart'] : substr($seance->heure_fin, 0, 5);

                $debut = Carbon::parse($this->date . ' ' . $arrivee);
                $fin = Carbon::parse($this->date . ' ' . $depart);
                if ($fin->lessThanOrEqualTo($debut)) {
                    $fin->addDay();      // passage à minuit
                }
                $nombreHeure = round($debut->diffInMinutes($fin) / 60, 2);

                $donnees = [
                    'enseignant_id' => $seance->enseignant_id,
                    'classe_etude_id' => $seance->classe_id,
                    'matiere_etude_id' => $seance->matiere_id,
                    'salle_etude_id' => $seance->salle_id,
                    'seance_id' => $seance->id,
                    'date' => $this->date,
                    'heure_arrivee' => $arrivee,
                    'heure_depart' => $depart,
                    'nombre_heure' => $nombreHeure,
                    'status' => $l['status'],
                    'annee_scolaire_id' => $anneeScolaire->id,
                ];

                $existant = !empty($l['emargement_id'])
                    ? EmargementEnseignantModel::find($l['emargement_id'])
                    : null;

                if ($existant) {
                    $existant->update($donnees);
                    $modifies++;
                } else {
                    EmargementEnseignantModel::create($donnees);
                    $crees++;
                }
            }
        });

        $this->chargerLignes();

        if ($ignores) {
            session()->flash($crees + $modifies ? 'success' : 'danger', "Émargement enregistré : $crees créé(s), $modifies mis à jour. $ignores ligne(s) ignorée(s) : enseignant parti avant cette date.");
            return;
        }

        session()->flash('success', "Émargement enregistré : $crees créé(s), $modifies mis à jour.");
    }

    /* ------------------------------------------------------------------ */
    /*  Historique                                                         */
    /* ------------------------------------------------------------------ */

    public function updatingEnseignantfilter() { $this->resetPage(); }
    public function updatingClassefilter()     { $this->resetPage(); }
    public function updatingDateDebut()        { $this->resetPage(); }
    public function updatingDateFin()          { $this->resetPage(); }

    /**
     * Clé « enseignant-mois » : une paie est calculée par enseignant et par mois.
     */
    protected function clePaie($enseignantId, $date): string
    {
        return $enseignantId . '-' . Carbon::parse($date)->month;
    }

    /**
     * Clés (enseignant-mois) de l'année scolaire active dont la paie est déjà
     * PAYÉE : les émargements de ces mois sont verrouillés.
     */
    protected function moisPayes(): array
    {
        $annee = AnneeScolaire();
        if (!$annee) {
            return [];
        }

        return EtatPaieEnseignantModel::where('annee_scolaire_id', $annee->id)
            ->where('statut', 'payé')
            ->get(['enseignant_id', 'mois_id'])
            ->mapWithKeys(fn ($e) => [$e->enseignant_id . '-' . (int) $e->mois_id => true])
            ->all();
    }

    /**
     * Supprime des émargements en respectant la règle de paie :
     *  - mois déjà payé pour cet enseignant  => suppression refusée ;
     *  - mois seulement calculé (en attente) => suppression autorisée et le
     *    calcul de paie de l'enseignant pour ce mois est réinitialisé
     *    (il sera recalculé à la prochaine génération de la paie).
     *
     * @return array{0:int,1:int} [supprimés, bloqués]
     */
    protected function supprimerEmargements($emargements): array
    {
        $payes = $this->moisPayes();
        $anneeId = AnneeScolaire()?->id;

        $supprimables = $emargements->reject(
            fn ($e) => isset($payes[$this->clePaie($e->enseignant_id, $e->date)])
        );
        $bloques = $emargements->count() - $supprimables->count();

        if ($supprimables->isNotEmpty()) {
            DB::transaction(function () use ($supprimables, $anneeId) {
                // Réinitialiser le calcul (non payé) de chaque enseignant/mois touché
                foreach ($supprimables->groupBy(fn ($e) => $this->clePaie($e->enseignant_id, $e->date)) as $groupe) {
                    $premier = $groupe->first();

                    EtatPaieEnseignantModel::where('enseignant_id', $premier->enseignant_id)
                        ->where('mois_id', Carbon::parse($premier->date)->month)
                        ->where('annee_scolaire_id', $anneeId)
                        ->where('statut', 'en_attente')
                        ->delete();
                }

                EmargementEnseignantModel::whereIn('id', $supprimables->pluck('id'))->delete();
            });
        }

        return [$supprimables->count(), $bloques];
    }

    public function DeleteEmargement($id)
    {
        if (empty(Permission('Supprimer'))) {
            return;
        }

        $emargement = EmargementEnseignantModel::find($id);
        if (!$emargement) {
            return;
        }

        [$supprimes, $bloques] = $this->supprimerEmargements(collect([$emargement]));
        $this->chargerLignes();

        if ($bloques) {
            session()->flash('danger', "Suppression impossible : la paie de cet enseignant pour ce mois est déjà payée.");
            return;
        }

        session()->flash('danger', 'Cet émargement est supprimé avec succès. Le calcul de paie en attente de cet enseignant pour ce mois a été réinitialisé.');
    }

    /** Suppression groupée des lignes cochées dans l'historique. */
    public function supprimerSelection()
    {
        if (empty(Permission('Supprimer')) || !$this->selection) {
            return;
        }

        $emargements = EmargementEnseignantModel::whereIn('id', $this->selection)->get();
        [$supprimes, $bloques] = $this->supprimerEmargements($emargements);

        $this->selection = [];
        $this->chargerLignes();

        $message = "$supprimes émargement(s) supprimé(s).";
        if ($bloques) {
            $message .= " $bloques non supprimé(s) : paie du mois déjà payée pour l'enseignant.";
        }

        session()->flash('danger', $message);
    }

    public function render()
    {
        $teachers = EnseignantModel::orderBy('nom')->get();
        $classes = ClasseEtudeModel::orderBy('classe')->get();
        $anneeScolaire = AnneeScolaire();

        // Aucune année scolaire active : liste vide + avertissement dans la vue
        if (!$anneeScolaire) {
            return view('livewire.scolarite.emargement-enseignant', [
                'teachers' => $teachers,
                'classes' => $classes,
                'emargements' => EmargementEnseignantModel::whereRaw('1 = 0')->paginate(20),
                'anneeScolaireActive' => false,
                'moisPayes' => [],
            ]);
        }

        $emargements = EmargementEnseignantModel::with(['enseignant', 'matiere', 'classe', 'salle'])
            ->where('annee_scolaire_id', $anneeScolaire->id)
            ->when($this->enseignantfilter, fn ($q) => $q->where('enseignant_id', $this->enseignantfilter))
            ->when($this->classefilter, fn ($q) => $q->where('classe_etude_id', $this->classefilter))
            ->when($this->dateDebut, fn ($q) => $q->whereDate('date', '>=', $this->dateDebut))
            ->when($this->dateFin, fn ($q) => $q->whereDate('date', '<=', $this->dateFin))
            ->orderBy('date', 'DESC')
            ->orderBy('heure_arrivee', 'ASC')
            ->paginate(20);

        return view('livewire.scolarite.emargement-enseignant', [
            'teachers' => $teachers,
            'classes' => $classes,
            'emargements' => $emargements,
            'anneeScolaireActive' => true,
            'moisPayes' => $this->moisPayes(),
        ]);
    }
}
