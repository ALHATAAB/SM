<?php

use App\Http\Controllers\BulletinScolaireController;
use App\Livewire\Programme\ApprenantParClasse;
use App\Livewire\Programme\BulletinScolaire;
use App\Livewire\Programme\EmploiDuTemps;
use Illuminate\Support\Facades\Route;


Route::get('/emplois/temps', EmploiDuTemps::class);
Route::get('/liste/classe', ApprenantParClasse::class);
Route::get('/bulletin/scolaire', BulletinScolaire::class);
Route::get('/bulletin/scolaire/imprimer', [BulletinScolaireController::class, 'imprimer']);
