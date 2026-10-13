<?php

namespace App\Http\Controllers;

use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\TrimestreModel;
use App\Services\BulletinService;
use App\Services\SocieteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Impression des bulletins scolaires : le PDF s'ouvre dans le navigateur
 * (nouvel onglet) pour être imprimé ou enregistré.
 *
 * GET /bulletin/scolaire/imprimer?classe={id}&periode={id trimestre|annuel}[&eleve={id inscription}]
 *   - sans « eleve » : tous les bulletins de la classe
 *   - avec « eleve » : le bulletin d'un seul élève
 */
class BulletinScolaireController extends Controller
{
    public function imprimer(Request $request, BulletinService $service)
    {
        abort_unless(Auth::check(), 401);
        abort_if(empty(Permission('Programme')), 403, 'Accès refusé.');

        $annee = AnneeScolaire();
        abort_unless($annee, 404, "Pas d'année scolaire active.");

        $classeId = (int) $request->query('classe');
        $classe = ClasseEtudeModel::findOrFail($classeId);

        $demande = (string) $request->query('periode', '');
        abort_if($demande === '', 404, 'Période manquante.');
        $periode = $demande === BulletinService::ANNUEL ? BulletinService::ANNUEL : (int) $demande;

        ini_set('max_execution_time', 3600);

        $donnees = $service->classe($classe->id, $annee->id, $periode);
        $eleves = $donnees['eleves'];

        if ($request->filled('eleve')) {
            $id = (int) $request->query('eleve');
            abort_unless(isset($eleves[$id]), 404, 'Élève introuvable dans cette classe.');
            $fichier = 'bulletin-' . Str::slug($eleves[$id]['nom'] . '-' . $eleves[$id]['prenom']) . '.pdf';
            $eleves = [$eleves[$id]];
        } else {
            $fichier = 'bulletins-' . Str::slug($classe->classe) . '.pdf';
            $eleves = array_values($eleves);
        }

        abort_if(empty($eleves), 404, 'Aucun élève inscrit dans cette classe.');

        $libellePeriode = $periode === BulletinService::ANNUEL
            ? 'Année complète'
            : (string) TrimestreModel::whereKey($periode)->value('trimestre');

        $pdf = Pdf::loadView('livewire.print.bulletin_scolaire', [
            'bulletins'    => $eleves,
            'stats'        => $donnees['stats'],
            'annuel'       => $donnees['annuel'],
            'trimestres'   => $donnees['trimestres'],
            'classeNom'    => $classe->classe,
            'anneeLabel'   => $annee->annee,
            'periodeLabel' => $libellePeriode,
            'ecole'        => SocieteService::info(),
        ])->setPaper('A4', 'portrait');

        // stream() : affichage dans le navigateur (et non téléchargement forcé)
        return $pdf->stream($fichier);
    }
}
