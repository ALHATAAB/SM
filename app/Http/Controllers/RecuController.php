<?php

namespace App\Http\Controllers;

use App\Services\RecuService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Impression des reçus (pré-inscription, inscription, scolarité, abonnement, salaire, enseignant).
 * GET /recu/imprimer/{type}/{id chiffré}?format=a4|t80|t85 : le PDF s'ouvre dans le navigateur.
 */
class RecuController extends Controller
{
    public function imprimer(Request $request, string $type, string $id, RecuService $service)
    {
        abort_unless(Auth::check(), 401);
        abort_unless(isset(RecuService::TYPES[$type]), 404);
        abort_unless($this->autorise($type), 403, 'Accès refusé.');

        try {
            $id = (int) decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }
        abort_unless($id > 0, 404);

        $demande = $request->query('format');
        $format = RecuService::formatValide(is_string($demande) ? $demande : session('recu_format'));
        session(['recu_format' => $format]);

        ini_set('max_execution_time', 300);

        $donnees = $service->donnees($type, $id);

        return $service->pdf($donnees, $format)->stream('recu-' . $type . '-' . $id . '.pdf');
    }

    protected function autorise(string $type): bool
    {
        foreach (RecuService::TYPES[$type] as $slug) {
            if (!empty(Permission($slug))) {
                return true;
            }
        }

        return false;
    }
}
