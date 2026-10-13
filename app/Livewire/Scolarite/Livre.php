<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\LivreModel;
use Livewire\Component;
use Livewire\WithPagination;

class Livre extends Component
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
            return view('livewire.scolarite.livre',['livres'=>$livres]);
        }else{
            $livres = LivreModel::paginate(500);
            return view('livewire.scolarite.livre',['livres'=>$livres]);
        }
    }

    public function addLivre()
    {
        $validated = $this->validate(
            [
                'titre'=>'required',
                'auteur'=>'required',
                'prix_achat'=>'required',
                'prix_vente'=>'required',
            ]
        );
        
           LivreModel::create([
            'titre'=>$validated['titre'],
            'auteur'=>$validated['auteur'],
            'prix_achat'=>$validated['prix_achat'],
            'prix_vente'=>$validated['prix_vente'],
        ]);

        return redirect('/livre')->with('success','Opération effectuée avec succès');
    }

     public function detailLivre($id){

        $livre = LivreModel::find($id);
        $this->livre_id= $id;
        $this->titre= $livre->titre;
        $this->auteur= $livre->auteur;
        $this->prix_achat= $livre->prix_achat;
        $this->prix_vente= $livre->prix_vente;
        $this->quantite_initial= $livre->stock_initial;
        $this->quantite_actuel= $livre->stock_actuel;
    }

     public function editLivre($id){

        $livre = LivreModel::find($id);
        $this->livre_id= $id;
        $this->titre= $livre->titre;
        $this->auteur= $livre->auteur;
        $this->prix_achat= $livre->prix_achat;
        $this->prix_vente= $livre->prix_vente;
    }

    public function updateLivre()
    {
   
        $livres = LivreModel::find( $this->livre_id);
        $validated = $this->validate([
            'titre'   => 'required|string|unique:livre_models,titre,' . $livres->id ,
            'auteur' => 'required',
            'prix_achat' => 'required',
            'prix_vente' => 'required', 
        ]);

        $livres->update([
            'titre'=>$validated['titre'],
            'auteur'=>$validated['auteur'],
            'prix_achat'=>$validated['prix_achat'],
            'prix_vente'=>$validated['prix_vente'],
        ]);
        return redirect('/livre')->with('info','Modification effectuée avec succès');
    }
}
