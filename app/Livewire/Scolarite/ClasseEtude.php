<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\ClasseEtudeModel;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use Livewire\WithPagination;

class ClasseEtude extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $classe_id;
    public $classe;


    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $classe_etudes = ClasseEtudeModel::where("classe",'like','%'.$this->recherche.'%')->orderBy('classe','ASC')
            ->paginate(5);
            return view('livewire.scolarite.classe-etude',['classe_etudes'=>$classe_etudes]);
        }else{
            $classe_etudes = ClasseEtudeModel::orderBy('classe','ASC')->paginate(5);
            return view('livewire.scolarite.classe-etude',['classe_etudes'=>$classe_etudes]);
        }
    }

    public function addClasseEtude(Request  $request)
    {
        $validated = $this->validate([
            'classe' => 'required|string|unique:classe_etude_models',
        ]);
       
            ClasseEtudeModel::create([
                'classe'=>$validated['classe'],
            ]);

            return redirect('/classe/etude')->with('success','Opération effectuée avec succès');


    }

    public function editClasseEtude($id){

        $classe_etude = ClasseEtudeModel::find($id);
        $this->classe_id= $id;
        $this->classe= $classe_etude->classe;
    }

    public function updateClasseEtude(Request  $request)
    {
   
        $classe_etudes = ClasseEtudeModel::find( $this->classe_id);
        $validated = $this->validate([
            'classe'   => 'required|string|unique:classe_etude_models,classe,' . $classe_etudes->id ,
           
        ]);

        $classe_etudes->update([
            'classe'=>$validated['classe'],
        ]);
        return redirect('/classe/etude')->with('info','Modification effectuée avec succès');
    }
   
    
}
