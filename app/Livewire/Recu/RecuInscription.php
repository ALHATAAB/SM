<?php

namespace App\Livewire\Recu;

use App\Livewire\Concerns\ChoixFormatRecu;
use App\Models\Scolarite\HistoriqueInscriptionModel;
use Livewire\Component;

class RecuInscription extends Component
{
    use ChoixFormatRecu;

    public $matricule;
    public $inscription;

    public function mount($id)
    {
        $id = decrypt($id);
        $this->inscription = HistoriqueInscriptionModel::find($id);
        $this->initialiserFormat();
    }

    public function render()
    {
        return view('livewire.recu.recu-inscription');
    }
}
