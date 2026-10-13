<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\TypeAbonnementModel;
use Livewire\Component;
use Livewire\WithPagination;

class TypeAbonnement extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    public $type_abonnement_id,$type_abonnement;
    public $Permission;

    public function render() 
    {
          //condition de recherche utilisateur
        if($this->recherche ){
            $type_abonnements = TypeAbonnementModel::where("type_abonnement",'like','%'.$this->recherche.'%')
            ->paginate(5);
            return view('livewire.scolarite.type-abonnement',['type_abonnements'=>$type_abonnements]);
        }else{
            $type_abonnements = TypeAbonnementModel::paginate(5);
            return view('livewire.scolarite.type-abonnement',['type_abonnements'=>$type_abonnements]);
        }
    }

     public function addTypeAbonnement()
    {
        $validated = $this->validate(
            [
                'type_abonnement'=>'required|unique:type_abonnement_models',
            ]
        );

        TypeAbonnementModel::create([
            'type_abonnement'=>$validated['type_abonnement'],
        ]);

        return redirect('/type/abonnement')->with('success','Opération effectuée avec succès');
    }

    //editer un utilisateur
    public function editTypeAbonnement($id){
        $type_abonnement = TypeAbonnementModel::find($id);
        $this->type_abonnement_id= $id;
        $this->type_abonnement= $type_abonnement->type_abonnement;
    }
    public function updateTypeAbonnement()
    {
        $type_abonnement = TypeAbonnementModel::find( $this->type_abonnement_id);
        $validated = $this->validate(
            [
                'type_abonnement'=>'required|unique:type_abonnement_models,id,'.$type_abonnement->id,
            ]);

        $type_abonnement->update([
                  'type_abonnement'=>$validated['type_abonnement'],
        ]);

        return redirect('/type/abonnement')->with('info','Modification effectuée avec succès');
    }

    
}
