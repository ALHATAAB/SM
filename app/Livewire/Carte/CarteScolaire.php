<?php

namespace App\Livewire\Carte;

use App\Services\CarteService;

class CarteScolaire extends GestionCarte
{
    protected function categorie(): string
    {
        return CarteService::SCOLAIRE;
    }
}
