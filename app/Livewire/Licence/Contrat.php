<?php

namespace App\Livewire\Licence;

use App\Services\LicenceService;
use Livewire\Component;

class Contrat extends Component
{
    public function render()
    {
        return view('livewire.licence.contrat', [
            'etat' => app(LicenceService::class)->etatComplet(),
        ]);
    }
}
