<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\FraisAbonnementModel;
use App\Models\Scolarite\TypeAbonnementModel;
use Livewire\Component;

class FraisAbonnement extends Component
{

    public $type_abonnements =[];
    public $periodes = ['Jour(s)', 'Mois', 'Année(s)'];
    public $nombres = [ '1','2','3','4','5','6','7','8','9','10', 
                        '11','12','13','14','15','16','17','18','19','20', 
                        '21','22','23','24','25','26','27','28','29','30', 
                        '31', 
                    ];
    public $type_abonnement,$periode,$nombre, $frais, $frais_id;
    public $fraisAbonnements=[];

    public function mount()
    {
        $this->periodes;
        $this->type_abonnements = TypeAbonnementModel::all();
        $this->fraisAbonnements = FraisAbonnementModel::with(['type_abonnement'])->get();
    }

    public function render()
    {
        return view('livewire.scolarite.frais-abonnement');
    }


     public function AddFraisAbonnement()
    {
        $validated = $this->validate(
            [
                'type_abonnement'=>'required',
                'periode'=>'required',
                'nombre'=>'required',
                'frais'=>'required',
            ]
        );
     $fraisAbonnement = FraisAbonnementModel::where('type_abonnement_id',$validated['type_abonnement'])
                        ->where('periode',$validated['periode'])
                        ->where('nombre',$validated['nombre'])
                        ->count();
        if(!empty($fraisAbonnement) &&  $fraisAbonnement> 0 ){
            return redirect('/frais/abonnement/')->with('danger','Cette Opération existe déjà');
        }else{
            FraisAbonnementModel::create([
                'type_abonnement_id'=>$validated['type_abonnement'],
                'periode'=>$validated['periode'],
                'nombre'=>$validated['nombre'],
                'frais_abonnement'=>$validated['frais'],
            ]);

            return redirect('/frais/abonnement/')->with('success','Opération effectuée avec succès');
        }
     
    }

    public function editFraisAbonnement($id){

        $frais_abonnement = FraisAbonnementModel::find($id);
        $this->frais_id= $id;
        $this->type_abonnement= $frais_abonnement->type_abonnement_id;
        $this->periode= $frais_abonnement->periode;
        $this->nombre= $frais_abonnement->nombre;
        $this->frais= $frais_abonnement->frais_abonnement;
    }

    public function updateFraisAbonnement()
    {
   
        $frais_abonnements = FraisAbonnementModel::find( $this->frais_id);
        $validated = $this->validate([
                'type_abonnement'=>'required',
                'periode'=>'required',
                'nombre'=>'required',
                'frais'=>'required',
        ]);

        $frais_abonnements->update([
            'type_abonnement_id'=>$validated['type_abonnement'],
            'periode'=>$validated['periode'],
            'nombre'=>$validated['nombre'],
            'frais_abonnement'=>$validated['frais'],
        ]);

        return redirect('/frais/abonnement')->with('info','Modification effectuée avec succès');
    }
    
}
