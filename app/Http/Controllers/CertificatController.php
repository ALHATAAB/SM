<?php

namespace App\Http\Controllers;

use App\Models\Certificat\CertificatModel;
use App\Services\CertificatService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Impression (PDF A4) des certificats de scolarité et de travail. */
class CertificatController extends Controller
{
    public function imprimer(string $id, CertificatService $service)
    {
        try {
            $id = (int) decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $certificat = CertificatModel::findOrFail($id);

        abort_unless(Auth::check(), 401);
        abort_if(empty(Permission($certificat->personne_type === 'employer' ? 'Employer' : 'Scolarite')), 403, 'Accès refusé.');
        abort_unless($certificat->estValide(), 403, "Ce certificat n'est plus valable : impression impossible.");

        ini_set('max_execution_time', 300);

        $donnees = $service->donnees($certificat);

        CertificatModel::whereKey($certificat->id)->update([
            'date_impression'    => now(),
            'nombre_impressions' => DB::raw('nombre_impressions + 1'),
        ]);

        $vue = $certificat->type === CertificatService::SCOLARITE ? 'livewire.print.certificat_scolarite' : 'livewire.print.certificat_travail';

        return Pdf::loadView($vue, $donnees)
            ->setPaper('a4', 'portrait')
            ->stream('certificat-' . Str::slug($certificat->numero) . '.pdf');
    }
}
