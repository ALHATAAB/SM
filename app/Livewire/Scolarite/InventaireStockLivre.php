<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\LivreModel;
use Livewire\Component;
use Livewire\WithPagination;

class InventaireStockLivre extends Component
{

     use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    // public $livres;
    public $titre,$auteur,$prix_achat,$prix_vente,$quantite_initial,$quantite_actuel,$livre_id;

    public function render()
    {
          //condition de recherche utilisateur
          if($this->recherche){
            $livres = LivreModel::where("nom",'like','%'.$this->recherche.'%')
            ->paginate(500);
            return view('livewire.scolarite.inventaire-stock-livre',['livres'=>$livres]);
        }else{
            $livres = LivreModel::paginate(500);
            return view('livewire.scolarite.inventaire-stock-livre',['livres'=>$livres]);
        }
    }
}
