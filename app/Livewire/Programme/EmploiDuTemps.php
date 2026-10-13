<?php

namespace App\Livewire\Programme;

use App\Models\Programme\EmploiDuTempsModel;
use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\SalleEtudeModel;
use App\Models\Scolarite\TrimestreModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

/**
 * Emploi du temps — tableau croisé en saisie groupée.
 *
 * Lignes  = créneaux horaires (1re colonne), colonnes = jours de la semaine.
 * Chaque cellule contient une séance (matière, salle, enseignant ou classe).
 * Un SEUL clic sur « Enregistrer » crée, modifie et supprime l'ensemble des
 * séances de la classe (ou de l'enseignant) en une transaction.
 */
class EmploiDuTemps extends Component
{
    public $type = 'classe';          // 'classe' ou 'enseignant'
    public $selectedId;
    public $items = [];
    public $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    /** Créneaux horaires (lignes du tableau) : [['debut' => '08:00', 'fin' => '10:00'], ...] */
    public array $slots = [];

    /** Cellules : $grid[index créneau][jour] = ['id', 'matiere_id', 'salle_id', 'autre_id'] */
    public array $grid = [];

    /** Dimanche masqué par défaut (affiché si une séance existe ce jour-là). */
    public bool $afficherDimanche = false;

    /** Ids des séances chargées dans la grille (sert à détecter les suppressions). */
    public array $loadedIds = [];

    /** Messages de conflit affichés au-dessus de la grille. */
    public array $conflits = [];

    public function mount()
    {
        $this->items = ClasseEtudeModel::orderBy('classe', 'ASC')->get();
    }

    /* ------------------------------------------------------------------ */
    /*  Sélection classe / enseignant                                      */
    /* ------------------------------------------------------------------ */

    public function updatedType()
    {
        $this->reset(['selectedId', 'slots', 'grid', 'loadedIds', 'conflits', 'afficherDimanche']);
        $this->resetErrorBag();
        $this->items = $this->type === 'classe'
            ? ClasseEtudeModel::orderBy('classe', 'ASC')->get()
            : EnseignantModel::orderBy('nom', 'ASC')->get();
    }

    public function updatedSelectedId()
    {
        $this->annuler();
    }

    /** Abandonne les modifications non enregistrées et recharge la grille. */
    public function annuler()
    {
        $this->conflits = [];
        $this->resetErrorBag();
        $this->chargerGrille();
    }

    protected function celluleVide(): array
    {
        return ['id' => null, 'matiere_id' => '', 'salle_id' => '', 'autre_id' => ''];
    }

    protected function ligneVide(): array
    {
        return array_fill_keys($this->jours, $this->celluleVide());
    }

    protected function chargerGrille(): void
    {
        $this->slots = [];
        $this->grid = [];
        $this->loadedIds = [];
        $this->afficherDimanche = false;

        if (!$this->selectedId) {
            return;
        }

        $seances = SeanceModel::where($this->type . '_id', $this->selectedId)->get();
        [$creneaux, $croise] = SeanceModel::tableauCroise($seances);

        foreach ($creneaux as $c) {
            $ligne = $this->ligneVide();

            foreach ($this->jours as $jour) {
                $seance = $croise[$c['key']][$jour][0] ?? null;
                if ($seance) {
                    $ligne[$jour] = [
                        'id' => $seance->id,
                        'matiere_id' => $seance->matiere_id,
                        'salle_id' => $seance->salle_id,
                        'autre_id' => $this->type === 'classe' ? $seance->enseignant_id : $seance->classe_id,
                    ];
                }
            }

            $this->slots[] = ['debut' => $c['debut'], 'fin' => $c['fin']];
            $this->grid[] = $ligne;
        }

        $this->loadedIds = $seances->pluck('id')->all();
        $this->afficherDimanche = $seances->contains('jour', 'Dimanche');

        if (!$this->slots) {
            $this->ajouterCreneau();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Manipulation du tableau (aucun accès base de données)              */
    /* ------------------------------------------------------------------ */

    public function ajouterCreneau()
    {
        if (!$this->selectedId) {
            return;
        }

        // Le nouveau créneau commence à la fin du dernier
        $dernier = collect($this->slots)->pluck('fin')->filter()->max();

        $this->slots[] = ['debut' => $dernier ?: '', 'fin' => ''];
        $this->grid[] = $this->ligneVide();
    }

    public function supprimerCreneau($index)
    {
        unset($this->slots[$index], $this->grid[$index]);
        $this->slots = array_values($this->slots);
        $this->grid = array_values($this->grid);
        $this->resetErrorBag();
    }

    public function trierCreneaux()
    {
        $ordre = collect($this->slots)->keys()
            ->sortBy(fn ($i) => $this->slots[$i]['debut'] . $this->slots[$i]['fin'])
            ->values();

        $this->slots = $ordre->map(fn ($i) => $this->slots[$i])->all();
        $this->grid = $ordre->map(fn ($i) => $this->grid[$i])->all();
        $this->resetErrorBag();
    }

    /** Recopie la première séance de la ligne sur les autres jours encore vides. */
    public function copierLigne($index)
    {
        if (!isset($this->grid[$index])) {
            return;
        }

        $visibles = $this->joursAffiches();
        $modele = null;

        foreach ($visibles as $jour) {
            if ($this->cellulePleine($this->grid[$index][$jour])) {
                $modele = $this->grid[$index][$jour];
                break;
            }
        }

        if (!$modele) {
            return;
        }

        $modele['id'] = null;
        foreach ($visibles as $jour) {
            if (!$this->cellulePleine($this->grid[$index][$jour])) {
                // si la cellule portait une séance (vidée), on la met à jour plutôt que d'en recréer une
                $this->grid[$index][$jour] = ['id' => $this->grid[$index][$jour]['id'] ?? null] + $modele;
            }
        }
    }

    public function viderCellule($index, $jour)
    {
        if (isset($this->grid[$index][$jour])) {
            // On garde l'id : à l'enregistrement, une cellule vidée supprime la séance
            $this->grid[$index][$jour] = ['id' => $this->grid[$index][$jour]['id']] + $this->celluleVide();
            $this->resetErrorBag("grid.$index.$jour");
        }
    }

    protected function cellulePleine(array $c): bool
    {
        return ($c['matiere_id'] ?? '') !== '' || ($c['salle_id'] ?? '') !== '' || ($c['autre_id'] ?? '') !== '';
    }

    public function joursAffiches(): array
    {
        return $this->afficherDimanche ? $this->jours : array_slice($this->jours, 0, 6);
    }

    /* ------------------------------------------------------------------ */
    /*  Enregistrement unique                                              */
    /* ------------------------------------------------------------------ */

    protected function trimestreActifId(?int $anneeId): ?int
    {
        if (!$anneeId) {
            return null;
        }

        $aujourdhui = now()->toDateString();

        return TrimestreModel::where('annee_scolaire_id', $anneeId)
            ->whereDate('date_debut', '<=', $aujourdhui)
            ->whereDate('date_fin', '>=', $aujourdhui)
            ->value('id')
            ?? TrimestreModel::where('annee_scolaire_id', $anneeId)
                ->orderByDesc('date_fin')
                ->value('id');
    }

    protected function emploiDuTempsId(int $classeId, int $anneeId, int $trimestreId): int
    {
        return EmploiDuTempsModel::firstOrCreate([
            'classe_id' => $classeId,
            'trimestre_id' => $trimestreId,
            'annee_scolaire_id' => $anneeId,
        ])->id;
    }

    public function saveAll()
    {
        $this->conflits = [];
        $this->resetErrorBag();

        if (empty(Permission('Ajouter')) && empty(Permission('Modifier'))) {
            session()->flash('danger', "Vous n'avez pas la permission d'enregistrer l'emploi du temps.");
            return;
        }

        if (!$this->selectedId) {
            session()->flash('danger', 'Sélectionnez d\'abord une classe ou un enseignant.');
            return;
        }

        $anneeId = optional(AnneeScolaire())->id;
        $trimestreId = $this->trimestreActifId($anneeId);

        if (!$anneeId || !$trimestreId) {
            session()->flash('danger', "Impossible d'enregistrer : aucune année scolaire active, ou aucun trimestre configuré pour l'année en cours.");
            return;
        }

        // 1) Créneaux horaires valides et distincts
        $erreurs = false;
        $vus = [];
        foreach ($this->slots as $i => $slot) {
            $debut = $slot['debut'] ?? '';
            $fin = $slot['fin'] ?? '';

            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $debut) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $fin)) {
                $this->addError("slots.$i", 'Heures requises');
                $erreurs = true;
            } elseif ($fin <= $debut) {
                $this->addError("slots.$i", 'Fin après début');
                $erreurs = true;
            } elseif (isset($vus["$debut-$fin"])) {
                $this->addError("slots.$i", 'Créneau en double');
                $erreurs = true;
            } else {
                $vus["$debut-$fin"] = true;
            }
        }

        // 2) Cellules : on transforme le tableau croisé en liste de séances
        $rows = [];
        $positions = [];       // position de chaque séance dans la grille, pour les messages d'erreur
        $autre = $this->type === 'classe' ? 'enseignant' : 'classe';

        foreach ($this->slots as $i => $slot) {
            foreach ($this->jours as $jour) {
                $c = $this->grid[$i][$jour] ?? $this->celluleVide();

                if (!$this->cellulePleine($c)) {
                    continue;        // cellule vide : pas de séance
                }

                if (($c['matiere_id'] ?? '') === '' || ($c['salle_id'] ?? '') === '' || ($c['autre_id'] ?? '') === '') {
                    $this->addError("grid.$i.$jour", "Matière, salle et $autre requis");
                    $erreurs = true;
                    continue;
                }

                $rows[] = [
                    'id' => $c['id'] ?? null,
                    'jour' => $jour,
                    'heure_debut' => $slot['debut'],
                    'heure_fin' => $slot['fin'],
                    'matiere_id' => $c['matiere_id'],
                    'salle_id' => $c['salle_id'],
                    'enseignant_id' => $this->type === 'classe' ? $c['autre_id'] : $this->selectedId,
                    'classe_id' => $this->type === 'classe' ? $this->selectedId : $c['autre_id'],
                ];
                $positions[] = [$i, $jour];
            }
        }

        // 3) Existence des références (matière, salle, enseignant, classe)
        if ($rows) {
            $v = Validator::make($rows, [
                '*.matiere_id' => 'exists:matiere_etude_models,id',
                '*.salle_id' => 'exists:salle_etude_models,id',
                '*.enseignant_id' => 'exists:enseignant_models,id',
                '*.classe_id' => 'exists:classe_etude_models,id',
            ]);

            if ($v->fails()) {
                foreach (array_keys($v->errors()->messages()) as $cle) {
                    [$n] = explode('.', $cle);
                    [$i, $jour] = $positions[(int) $n];
                    $this->addError("grid.$i.$jour", 'Valeur invalide');
                }
                $erreurs = true;
            }
        }

        if ($erreurs) {
            return;
        }

        // 4) Conflits dans le tableau, puis avec les séances déjà en base
        //    (hors séances de la grille : elles sont mises à jour ou supprimées ici)
        $conflits = SeanceModel::conflitsDansLot($rows);

        foreach ($rows as $k => $row) {
            $detail = SeanceModel::detailConflits($row, $this->loadedIds);

            if ($detail['messages']) {
                [$i, $jour] = $positions[$k];
                $this->addError("grid.$i.$jour", 'Conflit : ' . implode(', ', $detail['causes']));
                $conflits = array_merge($conflits, $detail['messages']);
            }
        }

        if ($conflits) {
            $this->conflits = $conflits;
            return;
        }

        // 5) Enregistrement atomique : tout ou rien
        DB::transaction(function () use ($rows, $anneeId, $trimestreId) {
            $emplois = [];
            $idsGardes = [];

            foreach ($rows as $row) {
                $classeId = (int) $row['classe_id'];
                $emplois[$classeId] ??= $this->emploiDuTempsId($classeId, $anneeId, $trimestreId);

                $data = [
                    'enseignant_id' => $row['enseignant_id'],
                    'classe_id' => $classeId,
                    'matiere_id' => $row['matiere_id'],
                    'salle_id' => $row['salle_id'],
                    'jour' => $row['jour'],
                    'heure_debut' => $row['heure_debut'],
                    'heure_fin' => $row['heure_fin'],
                    'emploi_du_temps_id' => $emplois[$classeId],
                ];

                if (!empty($row['id']) && in_array($row['id'], $this->loadedIds)) {
                    SeanceModel::whereKey($row['id'])->update($data);
                    $idsGardes[] = $row['id'];
                } else {
                    $idsGardes[] = SeanceModel::create($data)->id;
                }
            }

            // Cellules vidées ou créneaux retirés = séances supprimées
            $aSupprimer = array_diff($this->loadedIds, $idsGardes);
            if ($aSupprimer) {
                SeanceModel::whereIn('id', $aSupprimer)->delete();
            }
        });

        $nb = count($rows);
        $this->chargerGrille();

        session()->flash('success', "Emploi du temps enregistré : $nb séance(s).");
    }

    /* ------------------------------------------------------------------ */
    /*  Rendu et impressions                                               */
    /* ------------------------------------------------------------------ */

    public function render()
    {
        return view('livewire.programme.emploi-du-temps', [
            'enseignants' => EnseignantModel::orderBy('nom')->get(),
            'classes' => ClasseEtudeModel::orderBy('classe')->get(),
            'matieres' => MatiereEtudeModel::all(),
            'salles' => SalleEtudeModel::all(),
        ]);
    }

    protected function seancesPourImpression(string $champ, $id): array
    {
        $seances = SeanceModel::with(['matiere', 'enseignant', 'classe', 'salle'])
            ->where($champ, $id)
            ->get();

        [$creneaux, $grille] = SeanceModel::tableauCroise($seances);

        return ['creneaux' => $creneaux, 'grille' => $grille];
    }

    public function printEmpClasse($id)
    {
        $pdf = Pdf::loadView('livewire.print.emploi_du_temps', [
            'jours' => $this->jours,
            'classe' => ClasseEtudeModel::find($id),
        ] + $this->seancesPourImpression('classe_id', $id))->setPaper('A4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'emploi-du-temps.pdf');
    }

    public function printEmpEnseignant($id)
    {
        $pdf = Pdf::loadView('livewire.print.emploi_enseignant', [
            'jours' => $this->jours,
            'enseignant' => EnseignantModel::find($id),
        ] + $this->seancesPourImpression('enseignant_id', $id))->setPaper('A4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'emploi-du-temps.pdf');
    }
}
