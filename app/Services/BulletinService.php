<?php

namespace App\Services;

use App\Models\Scolarite\AbsenceApprenantModel;
use App\Models\Scolarite\CoefficientMatiereModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\NoteApprenantModel;
use App\Models\Scolarite\TrimestreModel;
use Illuminate\Support\Collection;

/**
 * Calcule les bulletins d'une classe pour l'année scolaire donnée, soit pour
 * un trimestre, soit en synthèse annuelle (periode = 'annuel').
 *
 * Seules les inscriptions actives (etat = 1) de l'année sont prises en compte,
 * comme pour le quota de licence.
 */
class BulletinService
{
    public const ANNUEL = 'annuel';

    /**
     * @return array{
     *   annuel: bool, periode: string, trimestres: array, matieres: array,
     *   eleves: array, stats: array
     * }
     */
    public function classe(int $classeId, int $anneeId, int|string $periode): array
    {
        $annuel = $periode === self::ANNUEL;

        $trimestres = TrimestreModel::where('annee_scolaire_id', $anneeId)
            ->orderBy('date_debut')->orderBy('id')->get();

        if (!$annuel) {
            $trimestres = $trimestres->where('id', (int) $periode)->values();
            abort_if($trimestres->isEmpty(), 404, 'Trimestre introuvable pour cette année scolaire.');
        }

        $inscriptions = InscriptionModel::with('preInscription')
            ->where('classe_etude_id', $classeId)
            ->where('annee_scolaire_id', $anneeId)
            ->where('etat', 1)
            ->get()
            ->sortBy(fn ($i) => mb_strtolower(($i->preInscription->nom ?? '') . ' ' . ($i->preInscription->prenom ?? '')))
            ->values();

        $coefficients = CoefficientMatiereModel::with('MatiereEtude')
            ->where('classe_etude_id', $classeId)
            ->get()
            ->filter(fn ($c) => $c->MatiereEtude !== null && (float) $c->coefficient > 0)
            ->sortBy(fn ($c) => mb_strtolower($c->MatiereEtude->matiere_etude))
            ->values();

        $notes = $this->notes($classeId, $trimestres, $inscriptions);

        // Moyennes par trimestre : [trimestre_id => [inscription_id => [matiere_id => moyenne|null]]]
        $moyennes = [];
        foreach ($trimestres as $t) {
            foreach ($inscriptions as $i) {
                foreach ($coefficients as $c) {
                    $moyennes[$t->id][$i->id][$c->matiere_etude_id] =
                        $this->moyenneMatiere($notes[$t->id][$i->id][$c->matiere_etude_id] ?? []);
                }
            }
        }

        $absences = $this->absences($trimestres, $inscriptions);

        $eleves = [];
        foreach ($inscriptions as $i) {
            $lignes = [];
            $points = 0.0;
            $coefTotal = 0.0;

            foreach ($coefficients as $c) {
                $mid  = $c->matiere_etude_id;
                $coef = (float) $c->coefficient;

                $parTrimestre = [];
                foreach ($trimestres as $t) {
                    $parTrimestre[$t->id] = $moyennes[$t->id][$i->id][$mid];
                }

                $disponibles = array_filter($parTrimestre, fn ($m) => $m !== null);
                $moyenne = $disponibles ? round(array_sum($disponibles) / count($disponibles), 2) : null;

                $premier = $trimestres->first();
                $noteMat = $annuel ? [] : ($notes[$premier->id][$i->id][$mid] ?? []);

                if ($moyenne !== null) {
                    $points    += $moyenne * $coef;
                    $coefTotal += $coef;
                }

                $lignes[] = [
                    'matiere'      => $c->MatiereEtude->matiere_etude,
                    'matiere_id'   => $mid,
                    'coef'         => $coef,
                    'classe'       => $noteMat['classe'] ?? null,
                    'composition'  => $noteMat['composition'] ?? null,
                    'trimestres'   => $parTrimestre,
                    'moyenne'      => $moyenne,
                    'total'        => $moyenne !== null ? round($moyenne * $coef, 2) : null,
                    'appreciation' => $moyenne !== null ? $this->appreciation($moyenne) : null,
                ];
            }

            $moyenneGenerale = $coefTotal > 0 ? round($points / $coefTotal, 2) : null;

            $eleves[$i->id] = [
                'inscription_id' => $i->id,
                'matricule'      => $i->matricule,
                'nom'            => $i->preInscription->nom ?? '',
                'prenom'         => $i->preInscription->prenom ?? '',
                'sexe'           => $i->preInscription->sexe ?? '',
                'date_naissance' => $i->preInscription->date_naissance ?? null,
                'lieu_naissance' => $i->preInscription->lieu_naissance ?? '',
                'lignes'         => $lignes,
                'total_points'   => round($points, 2),
                'total_coef'     => $coefTotal,
                'moyenne'        => $moyenneGenerale,
                'appreciation'   => $moyenneGenerale !== null ? $this->appreciation($moyenneGenerale) : null,
                'admis'          => $moyenneGenerale !== null && $moyenneGenerale >= (float) config('bulletin.moyenne_passage', 10),
                'rang'           => null,
                'effectif'       => $inscriptions->count(),
                'absences'       => $absences[$i->id] ?? ['total' => 0, 'justifiees' => 0],
            ];
        }

        $this->classerElevesEtMatieres($eleves);

        $valeurs = array_filter(array_column($eleves, 'moyenne'), fn ($m) => $m !== null);

        return [
            'annuel'     => $annuel,
            'periode'    => (string) $periode,
            'trimestres' => $trimestres->map(fn ($t) => ['id' => $t->id, 'nom' => $t->trimestre])->all(),
            'matieres'   => $coefficients->map(fn ($c) => ['id' => $c->matiere_etude_id, 'nom' => $c->MatiereEtude->matiere_etude, 'coef' => (float) $c->coefficient])->all(),
            'eleves'     => $eleves,
            'stats'      => [
                'effectif'   => $inscriptions->count(),
                'notes'      => count($valeurs),
                'moyenne'    => $valeurs ? round(array_sum($valeurs) / count($valeurs), 2) : null,
                'max'        => $valeurs ? max($valeurs) : null,
                'min'        => $valeurs ? min($valeurs) : null,
                'admis'      => count(array_filter($eleves, fn ($e) => $e['admis'])),
            ],
        ];
    }

    /**
     * Moyenne d'une matière à partir de la note de classe et de la composition.
     */
    public function moyenneMatiere(array $notes): ?float
    {
        $poids = config('bulletin.poids', ['classe' => 1, 'composition' => 1]);

        $somme = 0.0;
        $total = 0.0;

        foreach (['classe', 'composition'] as $type) {
            if (isset($notes[$type]) && $notes[$type] !== null && $notes[$type] !== '') {
                $p = (float) ($poids[$type] ?? 1);
                $somme += (float) $notes[$type] * $p;
                $total += $p;
            }
        }

        return $total > 0 ? round($somme / $total, 2) : null;
    }

    public function appreciation(float $moyenne): string
    {
        foreach (config('bulletin.appreciations', []) as [$seuil, $libelle]) {
            if ($moyenne >= $seuil) {
                return $libelle;
            }
        }

        return '';
    }

    /**
     * [trimestre_id => [inscription_id => [matiere_id => ['classe' => x, 'composition' => y]]]]
     */
    protected function notes(int $classeId, Collection $trimestres, Collection $inscriptions): array
    {
        if ($trimestres->isEmpty() || $inscriptions->isEmpty()) {
            return [];
        }

        $resultat = [];

        NoteApprenantModel::where('classe_etude_id', $classeId)
            ->whereIn('trimestre_id', $trimestres->pluck('id'))
            ->whereIn('apprenant_id', $inscriptions->pluck('id'))
            ->get(['trimestre_id', 'apprenant_id', 'matiere_etude_id', 'type', 'note'])
            ->each(function ($n) use (&$resultat) {
                $resultat[$n->trimestre_id][$n->apprenant_id][$n->matiere_etude_id][$n->type] = (float) $n->note;
            });

        return $resultat;
    }

    /**
     * Absences (séances manquées) sur la période, dont les justifiées.
     *
     * @return array<int, array{total:int, justifiees:int}>
     */
    protected function absences(Collection $trimestres, Collection $inscriptions): array
    {
        if ($trimestres->isEmpty() || $inscriptions->isEmpty()) {
            return [];
        }

        $debut = $trimestres->min('date_debut');
        $fin   = $trimestres->max('date_fin');

        $resultat = [];

        AbsenceApprenantModel::whereIn('inscription_id', $inscriptions->pluck('id'))
            ->whereBetween('date', [$debut, $fin])
            ->get(['inscription_id', 'justifiee'])
            ->each(function ($a) use (&$resultat) {
                $resultat[$a->inscription_id]['total']      = ($resultat[$a->inscription_id]['total'] ?? 0) + 1;
                $resultat[$a->inscription_id]['justifiees'] = ($resultat[$a->inscription_id]['justifiees'] ?? 0) + ($a->justifiee ? 1 : 0);
            });

        foreach ($resultat as $id => $r) {
            $resultat[$id]['justifiees'] = $r['justifiees'] ?? 0;
        }

        return $resultat;
    }

    /**
     * Rang général (ex æquo = même rang) et, pour chaque matière,
     * moyenne / plus forte / plus faible moyenne de la classe.
     */
    protected function classerElevesEtMatieres(array &$eleves): void
    {
        $moyennes = array_filter(array_column($eleves, 'moyenne'), fn ($m) => $m !== null);

        foreach ($eleves as &$e) {
            if ($e['moyenne'] !== null) {
                $e['rang'] = 1 + count(array_filter($moyennes, fn ($m) => $m > $e['moyenne']));
            }
        }
        unset($e);

        // Statistiques de classe par matière
        $parMatiere = [];
        foreach ($eleves as $e) {
            foreach ($e['lignes'] as $l) {
                if ($l['moyenne'] !== null) {
                    $parMatiere[$l['matiere_id']][] = $l['moyenne'];
                }
            }
        }

        foreach ($eleves as &$e) {
            foreach ($e['lignes'] as &$l) {
                $v = $parMatiere[$l['matiere_id']] ?? [];
                $l['moy_classe'] = $v ? round(array_sum($v) / count($v), 2) : null;
                $l['max_classe'] = $v ? max($v) : null;
                $l['min_classe'] = $v ? min($v) : null;
            }
            unset($l);
        }
        unset($e);
    }
}
