<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VerifieLicence;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
       $middleware->web(append:[
        SetLocale::class
       ]);
       $middleware->alias([
        'admin' => AdminMiddleware::class,
        'verifie.licence' => VerifieLicence::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Page dédiée quand la base de données ou ses tables sont absentes,
        // ou que le serveur de base de données est injoignable.
        $pageBaseDeDonnees = function (\Throwable $e, Request $request) {

            if ($request->expectsJson()) {
                return null; // laisse Laravel gérer la réponse JSON normalement
            }

            $message = $e->getMessage();

            $codeMysql = $e->errorInfo[1] ?? null; // code d'erreur MySQL sous-jacent

            if ($codeMysql === 1049 || str_contains($message, 'Unknown database')) {
                $type = 'base';
                $texte = "La base de données configurée pour l'application est introuvable.";
            } elseif (
                $codeMysql === 1146
                || str_contains($message, "doesn't exist")
                || str_contains($message, 'no such table')
            ) {
                $type = 'tables';
                $texte = "La base de données existe, mais une ou plusieurs tables nécessaires à l'application sont absentes.";
            } elseif (
                $codeMysql === 2002
                || str_contains($message, 'Connection refused')
                || str_contains($message, 'unable to open database file')
                || str_contains($message, 'could not find driver')
            ) {
                $type = 'connexion';
                $texte = "Impossible de se connecter au serveur de base de données.";
            } else {
                // Erreur de base de données d'un autre type (SQL invalide, etc.) :
                // on laisse Laravel afficher la page d'erreur habituelle.
                return null;
            }

            return response()->view('errors.base-de-donnees', [
                'type'     => $type,
                'message'  => $texte,
                'database' => config('database.connections.'.config('database.default').'.database'),
                'detail'   => $message,
            ], 503);
        };

        // QueryException (erreur pendant une requête) et PDOException (échec de connexion,
        // ex. base absente ou serveur injoignable) héritent toutes deux de PDOException.
        $exceptions->render(function (PDOException $e, Request $request) use ($pageBaseDeDonnees) {
            return $pageBaseDeDonnees($e, $request);
        });
    })->create();
