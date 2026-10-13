<?php

namespace App\Livewire\Recu;

use App\Livewire\Concerns\ChoixFormatRecu;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use App\Services\RecuService;
use Livewire\Component;

class RecuPaiementEnseignant extends Component
{
    use ChoixFormatRecu;

    public $matricule;
    public $paiement_enseignant;

    public function mount($id)
    {
        $id = decrypt($id);
        $this->paiement_enseignant = HistoriquePaiementEnseignantModel::find($id);
        $this->initialiserFormat();
    }

    public function render()
    {
        return view('livewire.recu.recu-paiement-enseignant', [
            'rubriques' => $this->paiement_enseignant
                ? app(RecuService::class)->rubriquesEnseignant($this->paiement_enseignant)
                : null,
        ]);
    }
}
