<?php

use App\Livewire\PaieRH\DetailEmployer;
use App\Livewire\PaieRH\EditerBulletinPaie;
use App\Livewire\PaieRH\EditerEtatPaie;
use App\Livewire\PaieRH\Employer;
use App\Livewire\PaieRH\EtatPaie;
use App\Livewire\PaieRH\ReglementPaie;
use App\Livewire\PaieRH\Rubrique;
use Illuminate\Support\Facades\Route;



Route::get('/employer', Employer::class);
Route::get('/detail/employer/{id}', DetailEmployer::class);
Route::get('/rubrique', Rubrique::class);
Route::redirect('/constante/paie', '/parametre/paieRH'); // les constantes sont dans Paramètres paie
Route::get('/etat/paie', EtatPaie::class);
Route::get('/reglement/paie', ReglementPaie::class);
Route::get('/editer/etat/paie/{mois_id}/', EditerEtatPaie::class);
Route::get('/editer/bulletin/paie/{mois_id}/', EditerBulletinPaie::class);