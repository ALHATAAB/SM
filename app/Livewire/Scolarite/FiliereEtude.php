<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class FiliereEtude extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $filiere_id;
    public $filiere;
    public $niveau_etudes=[];
    public $niveau_etude;

    public function mount()
    {

        //liste des roles
        $this->niveau_etudes = NiveauEtudeModel::all();

    }

    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $filiere_etudes = FiliereEtudeModel::where("filiere",'like','%'.$this->recherche.'%')->orderBy('filiere','DESC')
            ->paginate(5);
            return view('livewire.scolarite.filiere-etude',['filiere_etudes'=>$filiere_etudes]);
        }else{
            $filiere_etudes = FiliereEtudeModel::orderBy('filiere','DESC')->paginate(5);
            return view('livewire.scolarite.filiere-etude',['filiere_etudes'=>$filiere_etudes]);
        }
    }

    public function addFiliereEtude(Request  $request)
    {
        $validated = $this->validate([
            'filiere' => 'required|string|unique:filiere_etude_models',
        ]);
       
        FiliereEtudeModel::create([
                'filiere'=>$validated['filiere'],
            ]);

            return redirect('/filiere/etude')->with('success','Opération effectuée avec succès');


    }

    public function editFiliereEtude($id){

        $filiere_etude = FiliereEtudeModel::find($id);
        $this->filiere_id= $id;
        $this->filiere= $filiere_etude->filiere;
    }

    public function updateFiliereEtude(Request  $request)
    {
   
        $filiere_etudes = FiliereEtudeModel::find( $this->filiere_id);
        $validated = $this->validate([
            'filiere'   => 'required|string|unique:filiere_etude_models,filiere,' . $filiere_etudes->id ,
        ]);

        $filiere_etudes->update([
            'filiere'=>$validated['filiere'],
        ]);
        return redirect('/filiere/etude')->with('info','Modification effectuée avec succès');
    }
   
}
