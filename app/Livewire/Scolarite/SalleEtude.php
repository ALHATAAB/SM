<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\SalleEtudeModel;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;

class SalleEtude extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $salle_id;
    public $salle;
    public $capacite;


    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $salle_etudes = SalleEtudeModel::where("salle",'like','%'.$this->recherche.'%')->orderBy('salle','DESC')
            ->paginate(5);
            return view('livewire.scolarite.salle-etude',['salle_etudes'=>$salle_etudes]);
        }else{
            $salle_etudes = SalleEtudeModel::orderBy('salle','DESC')->paginate(5);
            return view('livewire.scolarite.salle-etude',['salle_etudes'=>$salle_etudes]);
        }
    }

    public function addSalleEtude(Request  $request)
    {
        $validated = $this->validate([
            'salle' => 'required|string|unique:salle_etude_models',
            'capacite' => 'required|integer',
        ]);
       
            SalleEtudeModel::create([
                'salle'=>$validated['salle'],
                'capacite'=>$validated['capacite'],
            ]);

            return redirect('/salle/etude')->with('success','Opération effectuée avec succès');


    }

    public function editSalleEtude($id){

        $salle_etude = SalleEtudeModel::find($id);
        $this->salle_id= $id;
        $this->salle= $salle_etude->salle;
        $this->capacite= $salle_etude->capacite;
    }

    public function updateSalleEtude(Request  $request)
    {
   
        $salle_etudes = SalleEtudeModel::find( $this->salle_id);
        $validated = $this->validate([
            'salle'   => 'required|string|unique:salle_etude_models,salle,' . $salle_etudes->id ,
            'capacite' => 'required|integer', 
        ]);

        $salle_etudes->update([
            'salle'=>$validated['salle'],
            'capacite'=>$validated['capacite'],
        ]);
        return redirect('/salle/etude')->with('info','Modification effectuée avec succès');
    }
   
}
