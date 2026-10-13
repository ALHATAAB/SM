<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VerifieLicence;
use Carbon\Carbon;
use Livewire\Livewire;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191); //Update defaultStringLength
    
        \Carbon\Carbon::setLocale('fr');

        // Les actions Livewire (clics, formulaires) passent par /livewire/update, pas par la page.
        // Sans ceci, les middlewares 'admin' (connexion) et 'verifie.licence' (licence expirée)
        // ne sont PAS revérifiés sur ces appels.
        Livewire::addPersistentMiddleware([
            AdminMiddleware::class,
            VerifieLicence::class,
        ]);
    }
}
