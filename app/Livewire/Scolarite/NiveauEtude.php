<?php

namespace App\Livewire\Scolarite;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;

class NiveauEtude extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $niveau_id;
    public $niveau;


    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $niveau_etudes = NiveauEtudeModel::where("niveau",'like','%'.$this->recherche.'%')->orderBy('niveau','DESC')
            ->paginate(5);
            return view('livewire.scolarite.niveau-etude',['niveau_etudes'=>$niveau_etudes]);
        }else{
            $niveau_etudes = NiveauEtudeModel::orderBy('niveau','DESC')->paginate(5);
            return view('livewire.scolarite.niveau-etude',['niveau_etudes'=>$niveau_etudes]);
        }
    }

    public function addNiveauEtude(Request  $request)
    {
        $validated = $this->validate([
            'niveau' => 'required|string|unique:niveau_etude_models',
        ]);
       
        NiveauEtudeModel::create([
                'niveau'=>$validated['niveau'],
            ]);

            return redirect('/niveau/etude')->with('success','Opération effectuée avec succès');


    }

    public function editNiveauEtude($id){

        $niveau_etude = NiveauEtudeModel::find($id);
        $this->niveau_id= $id;
        $this->niveau= $niveau_etude->niveau;
    }

    public function updateNiveauEtude(Request  $request)
    {
   
        $niveau_etudes = NiveauEtudeModel::find( $this->niveau_id);
        $validated = $this->validate([
            'niveau'   => 'required|string|unique:niveau_etude_models,niveau,' . $niveau_etudes->id ,
        ]);

        $niveau_etudes->update([
            'niveau'=>$validated['niveau'],
        ]);
        return redirect('/niveau/etude')->with('info','Modification effectuée avec succès');
    }

}
