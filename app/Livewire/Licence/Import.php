<?php

namespace App\Livewire\Licence;

use App\Models\LicenceModel;
use App\Services\LicenceService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use ZipArchive;

class Import extends Component
{
    use WithFileUploads;

    public $fichier_licence;

    public function importer()
    {
        $this->validate([
            'fichier_licence' => 'required|file|mimes:zip,xml|max:5120',
        ], [
            'fichier_licence.required' => 'Veuillez sélectionner un fichier de licence.',
            'fichier_licence.mimes'    => 'Le fichier doit être au format .zip ou .xml.',
            'fichier_licence.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $extension  = strtolower($this->fichier_licence->getClientOriginalExtension());
        $chemin     = $this->fichier_licence->getRealPath();
        $xmlContent = null;

        if ($extension === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($chemin) === true) {
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
            $xmlContent = file_get_contents($chemin);
        }

        if (empty($xmlContent)) {
            return $this->echec("Impossible de lire le fichier de licence fourni.");
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false || !isset($xml->CLE_ACTIVATION, $xml->INSCRIT, $xml->UTILISATEUR, $xml->DATE_DEBUT, $xml->DATE_FIN)) {
            return $this->echec("Le fichier de licence est invalide, corrompu, ou d'un format inattendu.");
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
            return $this->echec(
                "La licence a été importée mais n'est pas valable pour cette installation. " .
                "Vérifiez le LICENCE_ID_CLIENT dans le .env, et que les dates ne sont pas déjà expirées."
            );
        }

        $message = "Licence importée et activée avec succès !";
        if (!$licenceService->anneeLibelle()) {
            $message .= " Aucune année scolaire n'est active : cette licence sera rattachée à la prochaine année que vous activerez.";
        }

        session()->flash('succes', $message);

        return redirect()->route('licence.formulaire');
    }

    protected function echec(string $message): void
    {
        $this->reset('fichier_licence');
        session()->flash('erreur', $message);
    }

    public function render()
    {
        return view('livewire.licence.import', [
            'etat' => app(LicenceService::class)->etatComplet(),
        ]);
    }
}
