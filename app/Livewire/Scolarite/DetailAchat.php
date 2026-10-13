<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\AchatModel;
use Livewire\Component;

class DetailAchat extends Component
{
    public $achat;

    public function mount($id)
    {
         $id=decrypt($id);
         
        $this->achat = AchatModel::with(['ligneAchats'])->findOrFail($id);
    }
    
   public function annuler()
{
    if ($this->achat->statuts == "annuler") {
        session()->flash('error', 'Cet achat est déjà annulé.');
        return;
    }

    foreach ($this->achat->ligne_achat as $ligne) {
        $livre = $ligne->livre;

        if ($livre) {
            if ($livre->stock_actuel < $ligne->quantite) {
                     return redirect('achat/livre/detail/'.encrypt($this->achat->id))->with(
                    'warning',
                    "Stock insuffisant pour le livre [{$livre->titre}]. Stock actuel : {$livre->stock_actuel}, requis : {$ligne->quantite}."
                );
                return; // on arrête ici
            }
        }
    }

    // ✅ Tous les stocks sont suffisants, on peut annuler
    foreach ($this->achat->ligne_achat as $ligne) {
        $livre = $ligne->livre;

        if ($livre) {
            $livre->stock_actuel -= $ligne->quantite;
            $livre->save();
        }
    }

    $this->achat->update([
        'statuts' => "annuler",
    ]);

    $this->achat->refresh();

    session()->flash('success', 'Achat annulé avec succès.');
}


    public function render()
    {
        return view('livewire.scolarite.detail-achat');
    }
}
