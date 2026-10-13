<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\AchatModel;
use App\Models\Scolarite\LigneAchatModel;
use App\Models\Scolarite\LivreModel;
use Livewire\Component;

class LivreAchete extends Component
{

   
    public function render()
    {
        $achats = AchatModel::where('annee_scolaire_id', AnneeScolaire()->id)->latest()->paginate(10);
        return view('livewire.scolarite.livre-achete', [
            'achats' => $achats,
        ]);
    }

    
}
