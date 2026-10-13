<?php

namespace App\Livewire\Concerns;

use App\Services\RecuService;

/**
 * Choix du format d'impression d'un reçu (A4 / thermique 80 mm / thermique 85 mm).
 * Le choix est mémorisé en session : il suit l'utilisateur d'un reçu à l'autre.
 */
trait ChoixFormatRecu
{
    public string $format = '';

    protected function initialiserFormat(): void
    {
        $this->format = RecuService::formatValide(session('recu_format'));
    }

    public function updatedFormat($valeur): void
    {
        $this->format = RecuService::formatValide((string) $valeur);
        session(['recu_format' => $this->format]);
    }
}
