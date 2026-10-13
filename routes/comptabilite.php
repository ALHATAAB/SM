<?php

use App\Livewire\Comptabilite\ApproCaisse;
use App\Livewire\Comptabilite\Banque;
use App\Livewire\Comptabilite\Caisse;
use App\Livewire\Comptabilite\DateComptable;
use App\Livewire\Comptabilite\EncaissementDivers;
use App\Livewire\Comptabilite\EtatCaisse;
use App\Livewire\Comptabilite\GestionBanque;
use App\Livewire\Comptabilite\GestionCaisse;
use App\Livewire\Comptabilite\GestionDepense;
use App\Livewire\Comptabilite\HistoriqueAbonnement;
use App\Livewire\Comptabilite\HistoriqueInscription;
use App\Livewire\Comptabilite\HistoriquePaiementScolarite;
use Illuminate\Support\Facades\Route;




Route::get('/caisse', Caisse::class);
Route::get('/banque', Banque::class);
Route::get('/gestion/caisse', GestionCaisse::class);
Route::get('/gestion/depense', GestionDepense::class);
Route::get('/gestion/banque', GestionBanque::class);
Route::get('/encaissement/divers', EncaissementDivers::class);
Route::get('/date/comptable', DateComptable::class);
Route::get('/appro/caisse', ApproCaisse::class);
Route::get('/etat/caisse', EtatCaisse::class);

Route::get('/historique/paiement/scolarite', HistoriquePaiementScolarite::class);
Route::get('/historique/inscription',HistoriqueInscription::class);
Route::get('/historique/abonnement',HistoriqueAbonnement::class);
