<?php

namespace App\Http\Controllers;

use App\Models\Carte\CarteModel;
use App\Models\Scolarite\InscriptionModel;
use App\Services\CarteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Impression (PDF) des cartes scolaires et professionnelles : une carte ou un lot. */
class CarteController extends Controller
{
    private const LIMITE_LOT = 200;

    public function imprimer(string $id, CarteService $service)
    {
        try {
            $id = (int) decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $carte = CarteModel::findOrFail($id);
        $this->exiger($carte->personne_type);
        abort_unless($carte->estValide(), 403, "Cette carte n'est plus valable : impression impossible.");

        return $this->pdf(collect([$carte]), $service, 'carte-' . $carte->numero . '.pdf');
    }

    public function lot(Request $request, string $categorie, CarteService $service)
    {
        abort_unless(isset(CarteService::PERSONNES[$categorie]), 404);

        $anneeId = AnneeScolaire()?->id;
        abort_unless($anneeId, 404);

        $types = CarteService::PERSONNES[$categorie];
        if ($categorie === CarteService::PROFESSIONNELLE) {
            $types = [$request->query('population')];
            abort_unless(in_array($types[0], CarteService::PERSONNES[$categorie], true), 404);
        }
        foreach ($types as $type) {
            $this->exiger($type);
        }

        $requete = CarteModel::where('categorie', $categorie)->whereIn('personne_type', $types)
            ->where('annee_scolaire_id', $anneeId)->where('statut', 'active')
            ->where('date_expiration', '>=', now()->toDateString());

        // Cartes scolaires : filtre par classe (les élèves de la classe pour l'année active)
        $classe = $request->query('classe');
        if ($categorie === CarteService::SCOLAIRE && $classe !== null && $classe !== '') {
            $requete->whereIn('personne_id', InscriptionModel::where('annee_scolaire_id', $anneeId)
                ->where('etat', 1)->where('classe_etude_id', (int) $classe)->select('pre_inscription_id'));
        }

        $cartes = $requete->orderBy('numero')->limit(self::LIMITE_LOT)->get();
        abort_if($cartes->isEmpty(), 404, 'Aucune carte valide à imprimer pour cette sélection.');

        return $this->pdf($cartes, $service, 'cartes-' . $categorie . '.pdf');
    }

    /** Page ouverte en scannant le QR code : dit si la carte est valable. Réservée aux utilisateurs connectés. */
    public function verifier(string $numero, CarteService $service)
    {
        $carte = CarteModel::where('numero', $numero)->firstOrFail();
        $this->exiger($carte->personne_type);

        return view('carte.verification', [
            'c'      => $service->donnees($carte),
            'statut' => $carte->statutAffiche(),
        ]);
    }

    protected function pdf($cartes, CarteService $service, string $fichier)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $donnees = $cartes->map(fn ($c) => $service->donnees($c))->values()->all();

        CarteModel::whereIn('id', $cartes->pluck('id'))->update([
            'date_impression'    => now(),
            'nombre_impressions' => \Illuminate\Support\Facades\DB::raw('nombre_impressions + 1'),
        ]);

        return Pdf::loadView('livewire.print.cartes', ['cartes' => $donnees])
            ->setPaper('a4', 'portrait')
            ->stream($fichier);
    }

    protected function exiger(string $personneType): void
    {
        abort_unless(Auth::check(), 401);
        abort_if(empty(Permission($personneType === 'employer' ? 'Employer' : 'Scolarite')), 403, 'Accès refusé.');
    }
}
