<?php


use App\Http\Controllers\Lang\ControllerLanguage;
use App\Livewire\Config\Agence;
use App\Livewire\Config\Parametre;
use App\Livewire\Config\ParamPaie;
use App\Livewire\Config\Societe;
use Illuminate\Support\Facades\Route;



Route::get('/societe', Societe::class);
Route::get('/parametre', Parametre::class);
Route::get('/parametre/paieRH', ParamPaie::class);
Route::get('/agence', Agence::class);
Route::get('/change', [ControllerLanguage::class, 'change'])->name('change.lang');
