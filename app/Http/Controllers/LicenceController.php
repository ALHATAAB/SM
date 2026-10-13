<?php

namespace App\Http\Controllers;

use App\Models\LicenceModel;
use App\Services\LicenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class LicenceController extends Controller
{
    public function formulaire(LicenceService $licenceService)
    {
        return view('licence.import', [
            'etat' => $licenceService->etatComplet(),
        ]);
    }

    public function bloquee(LicenceService $licenceService)
    {
        return view('licence.bloquee', [
            'etat' => $licenceService->etatComplet(),
        ]);
    }

    public function contrat(LicenceService $licenceService)
    {
        return view('licence.contrat', [
            'etat' => $licenceService->etatComplet(),
        ]);
    }

    public function importer(Request $request)
    {
        $request->validate([
            'fichier_licence' => 'required|file|mimes:zip,xml|max:5120',
        ]);

        $fichier = $request->file('fichier_licence');
        $extension = strtolower($fichier->getClientOriginalExtension());
        $xmlContent = null;

        if ($extension === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($fichier->getRealPath()) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $nom = $zip->getNameIndex($i);
                    if (str_ends_with(strtolower($nom), '.xml')) {
                        $xmlContent = $zip->getFromIndex($i);
                        break;
                    }
                }
                $zip->close();
            }
        } else {
            $xmlContent = file_get_contents($fichier->getRealPath());
        }

        if (empty($xmlContent)) {
            return back()->with('erreur', "Impossible de lire le fichier de licence fourni.");
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false || !isset($xml->CLE_ACTIVATION, $xml->INSCRIT, $xml->UTILISATEUR, $xml->DATE_DEBUT, $xml->DATE_FIN)) {
            return back()->with('erreur', "Le fichier de licence est invalide, corrompu, ou d'un format inattendu.");
        }

        app(LicenceService::class)->enregistrerLicence([
            'DESIGNATION'    => (string) ($xml->DESIGNATION ?? ''),
            'DATE_DEBUT'     => (string) $xml->DATE_DEBUT,
            'DATE_FIN'       => (string) $xml->DATE_FIN,
            'INSCRIT'        => (int) $xml->INSCRIT,
            'UTILISATEUR'    => (int) $xml->UTILISATEUR,
            'PRIX_VENTES'    => (float) $xml->PRIX_VENTES,
            'CLE_ACTIVATION' => (string) $xml->CLE_ACTIVATION,
        ]);

        $licenceService = app(LicenceService::class);

        if (!$licenceService->estActive()) {
            return back()->with('erreur',
                "La licence a été importée mais n'est pas valable pour cette installation. " .
                "Vérifiez le LICENCE_ID_CLIENT dans le .env, et que les dates ne sont pas déjà expirées."
            );
        }

        $message = "Licence importée et activée avec succès !";
        if (!$licenceService->anneeLibelle()) {
            $message .= " Aucune année scolaire n'est active : cette licence sera rattachée à la prochaine année que vous activerez.";
        }

        return redirect()->route('licence.formulaire')->with('succes', $message);
    }
}