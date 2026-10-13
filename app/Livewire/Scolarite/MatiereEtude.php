<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\MatiereEtudeModel;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class MatiereEtude extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $filiere_id;
    public $matiere_id;
    public $matiere;

   

    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $matiere_etudes = MatiereEtudeModel::where("matiere_etude",'like','%'.$this->recherche.'%')->orderBy('matiere_etude','DESC')
            ->paginate(5);
            return view('livewire.scolarite.matiere-etude',['matiere_etudes'=>$matiere_etudes]);
        }else{
            $matiere_etudes = MatiereEtudeModel::orderBy('matiere_etude','DESC')->paginate(5);
            return view('livewire.scolarite.matiere-etude',['matiere_etudes'=>$matiere_etudes]);
        }
    }

    public function addMatiereEtude(Request  $request)
    {
        
        $validated = $this->validate([
            'matiere' => 'required|string',
        ]);
       
        MatiereEtudeModel::create([
                'matiere_etude'=>$validated['matiere'],
            ]);

            return redirect('/matiere/etude')->with('success','Opération effectuée avec succès');


    }

    public function editMatiereEtude($id){

        $matiere_etude = MatiereEtudeModel::find($id);
        $this->matiere_id= $id;
        $this->matiere= $matiere_etude->matiere_etude;
    }

    public function updateMatiereEtude(Request  $request)
    {
   
        $matiere_etudes = MatiereEtudeModel::find( $this->matiere_id);
        $validated = $this->validate([
            'matiere'   => 'required|string' ,
        ]);

        $matiere_etudes->update([
            'matiere_etude'=>$validated['matiere'],
        ]);
        return redirect('/matiere/etude')->with('info','Modification effectuée avec succès');
    }

}
