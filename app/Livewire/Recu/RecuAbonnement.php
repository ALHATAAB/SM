<?php

namespace App\Livewire\Recu;

use App\Livewire\Concerns\ChoixFormatRecu;
use App\Models\Scolarite\HistoriqueAbonnementModel;
use Livewire\Component;

class RecuAbonnement extends Component
{
    use ChoixFormatRecu;

    public $matricule;
    public $abonnement;

    public function mount($id)
    {
        $id = decrypt($id);
        $this->abonnement = HistoriqueAbonnementModel::find($id);
        $this->initialiserFormat();
    }

    public function render()
    {
        return view('livewire.recu.recu-abonnement');
    }
}
