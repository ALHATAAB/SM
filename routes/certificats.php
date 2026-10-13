<?php

use App\Http\Controllers\CertificatController;
use App\Livewire\Certificat\CertificatScolarite;
use App\Livewire\Certificat\CertificatTravail;
use Illuminate\Support\Facades\Route;

// Certificats de scolarité (élèves) et de travail (enseignants, personnel) : gestion + impression PDF.
Route::get('/certificat/scolarite', CertificatScolarite::class);
Route::get('/certificat/travail', CertificatTravail::class);
Route::get('/certificat/imprimer/{id}', [CertificatController::class, 'imprimer']);
