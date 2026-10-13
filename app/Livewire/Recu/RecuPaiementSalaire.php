<?php

namespace App\Livewire\Recu;

use App\Livewire\Concerns\ChoixFormatRecu;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use Livewire\Component;

class RecuPaiementSalaire extends Component
{
    use ChoixFormatRecu;

    public $matricule;
    public $paiement_salaire;

    public function mount($id)
    {
        $id = decrypt($id);
        $this->paiement_salaire = HistoriquePaiementSalaireModel::find($id);
        $this->initialiserFormat();
    }

    public function render()
    {
        return view('livewire.recu.recu-paiement-salaire');
    }
}
