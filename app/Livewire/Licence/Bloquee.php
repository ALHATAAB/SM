<?php

namespace App\Livewire\Licence;

use App\Services\LicenceService;
use Livewire\Component;

class Bloquee extends Component
{
    public function render()
    {
        return view('livewire.licence.bloquee', [
            'etat' => app(LicenceService::class)->etatComplet(),
        ]);
    }
}
