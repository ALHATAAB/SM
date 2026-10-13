<?php

use App\Livewire\Recu\RecuAbonnement;
use App\Livewire\Recu\RecuInscription;
use App\Livewire\Recu\RecuPaiementEnseignant;
use App\Livewire\Recu\RecuPaiementSalaire;
use App\Livewire\Recu\RecuPaiementScolarite;
use App\Livewire\Recu\RecuPreinscription;
use App\Http\Controllers\RecuController;
use Illuminate\Support\Facades\Route;





Route::get('/recu/preinscription/{id}', RecuPreinscription::class);
Route::get('/inscription/recu/{id}', RecuInscription::class);
Route::get('/reglement/frais/scolaire/recu/{id}', RecuPaiementScolarite::class);
Route::get('/reglement/salaire/recu/{id}', RecuPaiementSalaire::class);
Route::get('/reglement/enseignant/recu/{id}', RecuPaiementEnseignant::class);

Route::get('/abonnement/recu/{id}', RecuAbonnement::class);

// Impression PDF des reçus : format A4 ou thermique 80 / 85 mm, toujours en 2 exemplaires.
Route::get('/recu/imprimer/{type}/{id}', [RecuController::class, 'imprimer']);
