<?php

namespace App\Livewire\Recu;

use App\Livewire\Concerns\ChoixFormatRecu;
use App\Models\Scolarite\PreInscriptionModel;
use Livewire\Component;

class RecuPreinscription extends Component
{
    use ChoixFormatRecu;

    public $matricule;
    public $preinscription;

    public function mount($id)
    {
        $id = decrypt($id);
        $this->preinscription = PreInscriptionModel::find($id);
        $this->initialiserFormat();
    }

    public function render()
    {
        return view('livewire.recu.recu-preinscription');
    }
}
