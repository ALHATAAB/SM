<?php

namespace App\Livewire\Recu;

use App\Livewire\Concerns\ChoixFormatRecu;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Services\RecuService;
use Livewire\Component;

class RecuPaiementScolarite extends Component
{
    use ChoixFormatRecu;

    public $matricule;
    public $paiement_scolarisation;

    public function mount($id)
    {
        $id = decrypt($id);
        $this->paiement_scolarisation = HistoriquePaiementScolariteModel::find($id);
        $this->initialiserFormat();
    }

    public function render()
    {
        $service = app(RecuService::class);

        return view('livewire.recu.recu-paiement-scolarite', [
            'resteAPayer' => $this->paiement_scolarisation ? $service->resteAPayerScolarite($this->paiement_scolarisation) : null,
            'totalPaye'   => $this->paiement_scolarisation ? $service->totalPayeScolarite($this->paiement_scolarisation) : null,
            'nomBanque'   => $this->paiement_scolarisation ? $service->nomBanque($this->paiement_scolarisation) : null,
        ]);
    }
}
