<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\PreInscriptionModel;
use Livewire\Component;
use Livewire\WithPagination;

class RecherchePreinscrit extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public function render()
    {
       if (!empty($this->recherche)) {
    $query = PreInscriptionModel::where(function ($q) {
        $q->where("matricule", 'like', '%' . $this->recherche . '%')
          ->orWhere("nom", 'like', '%' . $this->recherche . '%')
          ->orWhere("prenom", 'like', '%' . $this->recherche . '%')
          ->orWhere("telephone", 'like', '%' . $this->recherche . '%');
    });

    $preinscriptionCount = $query->count();
    $preinscriptions = $query->paginate(25);
} else {
    $preinscriptionCount = 0;
    $preinscriptions = PreInscriptionModel::whereNull('id')->paginate(25); // paginator vide
}


        return view('livewire.scolarite.recherche-preinscrit', [
            'preinscriptions' => $preinscriptions,
            'preinscriptionCount' => $preinscriptionCount
        ]);
    }
}
