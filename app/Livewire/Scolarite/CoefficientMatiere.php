<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\CoefficientMatiereModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\FraisScolaireModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class CoefficientMatiere extends Component
{
    public $classes =[];
    public $niveaux =[];
    public $filieres=[];
    public $matieres=[];
    public $filiere_etudes=[];
    public $classe,$niveau, $filiere,$matiere, $coefficient, $coefficient_id, $niveau_id, $filiere_id;
    public $coefficientMatiere=[];
    public $coefficientCount;

    public function mount()
    {
        //liste des classe etude
        $this->classes = ClasseEtudeModel::all();
        //liste des niveau etude
        $this->niveaux = NiveauEtudeModel::all();
        //liste des filiere
        $this->filieres = FiliereEtudeModel::all();
        //liste des matiere
        $this->matieres = MatiereEtudeModel::all();

        // $this->coefficientMatiere = CoefficientMatiereModel::with(['niveauEtude', 'filiereEtude', 'matiereEtude'])->get();

    }
   
   

    // public function updatedNiveau($niveauId){

    //     $this->filiere_etudes= FraisScolaireModel::where('niveau_etude_id',$niveauId)->get();
    //     $this->niveau_id=$niveauId;
    //     $this->filiere='';
    // }

    public function updatedClasse($classeId){
       
            $this->coefficientCount = CoefficientMatiereModel::where('classe_etude_id',$classeId)
       ->count();
        $this->coefficientMatiere = CoefficientMatiereModel::where('classe_etude_id',$classeId)
        ->get();

        // dd( $this->coefficientMatiere );
    }


    public function render()
    {
        return view('livewire.scolarite.coefficient-matiere');
    }

    public function AddCoefficientMatiere(Request  $request)
    {
        $validated = $this->validate(
            [
                'classe'=>'required',
                'matiere'=>'required',
                'coefficient'=>'required|numeric',
            ]
        );
     $CoefficientMatiere = CoefficientMatiereModel::where('classe_etude_id',$validated['classe'])
     ->where('matiere_etude_id',$validated['matiere'])
     ->count();
    if(!empty($CoefficientMatiere) &&  $CoefficientMatiere > 0 ){
        return redirect('/coefficient/matiere/')->with('danger','Cette Opération existe déjà');
    }else{
        CoefficientMatiereModel::create([
            'classe_etude_id'=>$validated['classe'],
            'matiere_etude_id'=>$validated['matiere'],
            'coefficient'=>$validated['coefficient'],
        ]);

        return redirect('/coefficient/matiere/')->with('success','Opération effectuée avec succès');
    }
     
    }

    public function editCoefficientMatiere($id){
        $coefficientMatiere = CoefficientMatiereModel::find($id);
        $this->coefficient_id= $id;
        $this->classe= $coefficientMatiere->classe_etude_id;
        $this->matiere= $coefficientMatiere->matiere_etude_id;
        $this->coefficient= $coefficientMatiere->coefficient;
    }

    public function updateCoefficientMatiere(Request  $request)
    {
        $coefficientMatiere = CoefficientMatiereModel::find($this->coefficient_id);
        $validated = $this->validate(
            [
                'classe'=>'required',
                'matiere'=>'required',
                'coefficient'=>'required|numeric',
            ]
        );

        $coefficientMatiere->update([
            'classe_etude_id'=>$validated['classe'],
            'matiere_etude_id'=>$validated['matiere'],
            'coefficient'=>$validated['coefficient'],
        ]);

        return  redirect('/coefficient/matiere/')->with('info','Modification effectuée avec succès');
    }
}
