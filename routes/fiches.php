<?php

use App\Http\Controllers\FicheController;
use Illuminate\Support\Facades\Route;

// Fiches individuelles (PDF) : toutes les informations liées à l'élève, l'enseignant, l'employé.
Route::get('/fiche/apprenant/{id}', [FicheController::class, 'apprenant']);
Route::get('/fiche/enseignant/{id}', [FicheController::class, 'enseignant']);
Route::get('/fiche/employer/{id}', [FicheController::class, 'employer']);

// Photo d'une personne (élève, enseignant, employé), servie depuis storage/app/public.
Route::get('/photo/{type}/{id}', function (string $type, int $id) {
    abort_unless(isset(\App\Services\PhotoService::TYPES[$type]), 404);

    $chemin = \App\Services\PhotoService::chemin($type, $id);
    abort_unless($chemin, 404);

    return response()->file($chemin, ['Cache-Control' => 'private, max-age=86400']);
})->whereNumber('id')->name('photo.personne');

// Cartes scolaires et professionnelles : gestion (Livewire) et impression (PDF).
Route::get('/carte/scolaire', \App\Livewire\Carte\CarteScolaire::class);
Route::get('/carte/professionnelle', \App\Livewire\Carte\CarteProfessionnelle::class);
Route::get('/carte/imprimer/{id}', [\App\Http\Controllers\CarteController::class, 'imprimer']);
Route::get('/carte/lot/{categorie}', [\App\Http\Controllers\CarteController::class, 'lot']);
Route::get('/carte/verifier/{numero}', [\App\Http\Controllers\CarteController::class, 'verifier'])->where('numero', '[A-Za-z0-9\-]+');
