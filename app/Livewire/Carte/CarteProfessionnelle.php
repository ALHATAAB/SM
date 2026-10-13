<?php

namespace App\Livewire\Carte;

use App\Services\CarteService;

class CarteProfessionnelle extends GestionCarte
{
    protected function categorie(): string
    {
        return CarteService::PROFESSIONNELLE;
    }
}
