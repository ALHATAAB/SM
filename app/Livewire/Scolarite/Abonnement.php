<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\AbonnementModel;
use Livewire\Component;
use Livewire\WithPagination;

class Abonnement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
  
    public function render()
    {
            //condition de recherche utilisateur
          if($this->recherche){
            $abonnees = AbonnementModel::where("matricule",'like','%'.$this->recherche.'%')
            ->where('annee_scolaire_id', AnneeScolaire()->id)
            ->paginate(5);
            return view('livewire.scolarite.abonnement',['abonnees'=>$abonnees]);
        }else{
            $abonnees = AbonnementModel::where('annee_scolaire_id', AnneeScolaire()->id)->paginate(5);
            return view('livewire.scolarite.abonnement',['abonnees'=>$abonnees]);
        }
    }
}
