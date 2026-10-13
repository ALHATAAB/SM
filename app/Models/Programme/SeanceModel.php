<?php

namespace App\Models\Programme;

use App\Models\Programme\EmploiDuTempsModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\SalleEtudeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeanceModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'seance_models';

    public function enseignant() 
    {
        return $this->belongsTo(EnseignantModel::class,'enseignant_id');
    }

    public function matiere() 
    {
        return $this->belongsTo(MatiereEtudeModel::class,'matiere_id');
    }

    public function classe() 
    {
        return $this->belongsTo(ClasseEtudeModel::class,'classe_id');
    }

    public function salle() 
    {
        return $this->belongsTo(SalleEtudeModel::class,'salle_id');
    }

    public function emploiDuTemps() 
    {
        return $this->belongsTo(EmploiDuTempsModel::class,'emploi_du_temps_id');
    }


    /**
     * Séances déjà enregistrées qui entrent en conflit avec celle proposée :
     * même jour, créneau qui chevauche, et même enseignant, même salle ou
     * même classe.
     *
     * $excludeId : id (ou liste d'ids) de séance(s) à ignorer, par exemple
     * celles en cours de modification dans la grille de saisie groupée,
     * afin de ne pas les considérer comme étant en conflit avec elles-mêmes.
     */
    public static function conflitsAvec(array $data, int|array|null $excludeId = null)
    {
        $exclude = array_filter((array) $excludeId);

        return self::with(['enseignant', 'salle', 'classe', 'matiere'])
            ->where('jour', $data['jour'])
            ->when(!empty($exclude), fn ($query) => $query->whereNotIn('id', $exclude))
            ->where(function ($query) use ($data) {
                $query->where('enseignant_id', $data['enseignant_id'])
                    ->orWhere('salle_id', $data['salle_id'])
                    ->orWhere('classe_id', $data['classe_id']);
            })
            ->where(function ($query) use ($data) {
                // Chevauchement horaire classique : deux créneaux se
                // croisent dès que l'un commence avant que l'autre finisse.
                $query->where('heure_debut', '<', $data['heure_fin'])
                    ->where('heure_fin', '>', $data['heure_debut']);
            })
            ->get();
    }

    public static function hasConflict(array $data, int|array|null $excludeId = null): bool
    {
        return self::conflitsAvec($data, $excludeId)->isNotEmpty();
    }

    /**
     * Messages précis pour une séance proposée : une phrase par cause
     * (salle, enseignant, classe) et par séance déjà en place.
     *
     * @return array{causes: string[], messages: string[]}
     */
    public static function detailConflits(array $data, int|array|null $excludeId = null): array
    {
        $causes = [];
        $messages = [];

        foreach (self::conflitsAvec($data, $excludeId) as $e) {
            $quand = $e->jour . ' de ' . substr($e->heure_debut, 0, 5) . ' à ' . substr($e->heure_fin, 0, 5);
            $classe = $e->classe->classe ?? '?';
            $salle = $e->salle->salle ?? '?';
            $matiere = $e->matiere->matiere_etude ?? '?';
            $prof = trim(($e->enseignant->nom ?? '?') . ' ' . ($e->enseignant->prenom ?? ''));

            if ($e->salle_id == $data['salle_id']) {
                $causes['salle'] = 'salle';
                $messages[] = "Salle $salle déjà occupée par la classe $classe ($prof, $matiere) le $quand.";
            }
            if ($e->enseignant_id == $data['enseignant_id']) {
                $causes['enseignant'] = 'enseignant';
                $messages[] = "L'enseignant $prof a déjà cours avec la classe $classe ($matiere, salle $salle) le $quand.";
            }
            if ($e->classe_id == $data['classe_id']) {
                $causes['classe'] = 'classe';
                $messages[] = "La classe $classe a déjà cours de $matiere avec $prof (salle $salle) le $quand.";
            }
        }

        return ['causes' => array_values($causes), 'messages' => array_values(array_unique($messages))];
    }

    /**
     * Détecte les conflits À L'INTÉRIEUR d'un lot de séances (avant même
     * de les comparer à la base). Retourne la liste des paires en conflit
     * sous forme de messages lisibles ("Lundi 08:00-10:00 et Lundi 09:00-11:00 : ...").
     */
    public static function conflitsDansLot(array $rows): array
    {
        $messages = [];
        $n = count($rows);

        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $a = $rows[$i];
                $b = $rows[$j];

                if ($a['jour'] !== $b['jour']) {
                    continue;
                }
                if (!($a['heure_debut'] < $b['heure_fin'] && $a['heure_fin'] > $b['heure_debut'])) {
                    continue;
                }

                $causes = [];
                if ($a['enseignant_id'] == $b['enseignant_id']) $causes[] = 'même enseignant';
                if ($a['salle_id'] == $b['salle_id'])           $causes[] = 'même salle';
                if ($a['classe_id'] == $b['classe_id'])         $causes[] = 'même classe';

                if ($causes) {
                    $messages[] = $a['jour'] . ' ' . $a['heure_debut'] . '-' . $a['heure_fin']
                        . ' et ' . $b['jour'] . ' ' . $b['heure_debut'] . '-' . $b['heure_fin']
                        . ' : créneaux qui se chevauchent avec ' . implode(', ', $causes) . '.';
                }
            }
        }

        return $messages;
    }

    /**
     * Construit le tableau croisé d'un ensemble de séances :
     *  - $creneaux : liste triée des créneaux horaires [key, debut, fin] (1re colonne)
     *  - $grille   : $grille[key][jour] = liste de séances (colonnes = jours)
     *
     * @return array{0: array<int, array{key:string,debut:string,fin:string}>, 1: array}
     */
    public static function tableauCroise($seances): array
    {
        $creneaux = [];
        $grille = [];

        foreach ($seances as $s) {
            $debut = substr($s->heure_debut, 0, 5);
            $fin = substr($s->heure_fin, 0, 5);
            $key = $debut . '-' . $fin;

            $creneaux[$key] = ['key' => $key, 'debut' => $debut, 'fin' => $fin];
            $grille[$key][$s->jour][] = $s;
        }

        uasort($creneaux, fn ($a, $b) => [$a['debut'], $a['fin']] <=> [$b['debut'], $b['fin']]);

        return [array_values($creneaux), $grille];
    }
}
