<?php

namespace App\Http\Middleware;

use App\Services\LicenceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifieLicence
{
    /**
     * Contrôle la période d'expiration de la licence.
     * Si elle est absente, falsifiée ou hors de sa période de validité,
     * l'utilisateur est redirigé vers la page de blocage /licence/bloquee.
     *
     * Les routes /licence, /licence/importer, /licence/bloquee ne passent PAS
     * par ce middleware (voir routes/web.php) afin qu'un administrateur
     * connecté puisse toujours importer une nouvelle licence.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $licenceService = app(LicenceService::class);

        if (!$licenceService->estActive()) {
            return redirect()->route('licence.bloquee');
        }

        // Rend l'état de la licence disponible dans toutes les vues protégées
        // (utile pour afficher une bannière d'avertissement avant l'expiration).
        view()->share('licenceEtat', $licenceService->etatComplet());

        return $next($request);
    }
}
