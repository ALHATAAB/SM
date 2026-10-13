<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\VenteModel;
use Livewire\Component;

class LivreVendu extends Component
{

     public function render()
    {
        $ventes = VenteModel::where('annee_scolaire_id', AnneeScolaire()->id)->latest()->latest()->paginate(10);
        return view('livewire.scolarite.livre-vendu', [
            'ventes' => $ventes,
        ]);
    }
}
