<?php
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Livewire\Home;
use App\Livewire\Licence\Import;
use App\Livewire\Licence\Bloquee;
use App\Livewire\Licence\Contrat;

// Enregistrer une route de mise à jour personnalisée pour Livewire
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/SM/public/livewire/update', $handle); // Ajouter des middlewares supplémentaires
});

// Logo de la société : servi directement depuis storage/app/public, sans dépendre du
// lien symbolique public/storage. Route publique (le logo doit apparaître sur le login).
Route::get('/societe/logo', function () {
    $logo = \App\Models\Config\SocieteModel::value('logo');
    $disk = \Illuminate\Support\Facades\Storage::disk('public');

    $chemin = ($logo && $disk->exists($logo))
        ? $disk->path($logo)
        : public_path(\App\Services\SocieteService::LOGO_DEFAUT);

    return response()->file($chemin, ['Cache-Control' => 'public, max-age=86400']);
})->name('societe.logo');

// Routes de gestion de la licence : accessibles à tout administrateur connecté,
// MÊME si la licence est expirée (sinon personne ne pourrait jamais en importer une nouvelle).
// L'import du fichier est géré directement par le composant Livewire Import.
Route::middleware(['admin'])->group(function () {
    Route::get('/licence', Import::class)->name('licence.formulaire');
    Route::get('/licence/bloquee', Bloquee::class)->name('licence.bloquee');
    Route::get('/licence/contrat', Contrat::class)->name('licence.contrat');
});

// Pages admin protégées aussi par le contrôle de licence.
Route::middleware(['admin', 'verifie.licence'])->group(function () {
    Route::get('/home', Home::class)->name('home');

    require __DIR__.'/recu.php';
    require __DIR__.'/scolarite.php';
    require __DIR__.'/programme.php';
    require __DIR__.'/config.php';
    require __DIR__.'/comptabilite.php';
    require __DIR__.'/paieRH.php';
    require __DIR__.'/fiches.php';
    require __DIR__.'/certificats.php';
});

// Toujours à la fin
require __DIR__.'/auth.php';
