<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\FraisScolaireModel;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Http\Request;
use Livewire\Component;

class FraisEtude extends Component
{
    public $niveaux =[];
    public $filieres=[];
    public $niveau, $filiere,$inscription, $frais, $frais_id;
    public $fraisScolaires;

    public function mount()
    {

        //liste des niveau etude
        $this->niveaux = NiveauEtudeModel::all();
        //liste des roles
        $this->filieres = FiliereEtudeModel::all();

        $this->fraisScolaires = FraisScolaireModel::with(['niveauEtude', 'filiereEtude'])->get();
    
    }
    public function render()
    {
        return view('livewire.scolarite.frais-etude');
    }


    public function AddFraisEtude(Request  $request)
    {
        $validated = $this->validate(
            [
                'niveau'=>'required',
                'filiere'=>'required',
                'inscription'=>'required',
                'frais'=>'required',
            ]
        );
     $fraisScolaires = FraisScolaireModel::where('niveau_etude_id',$validated['niveau'])->where('filiere_etude_id',$validated['filiere'])->count();
        if(!empty($fraisScolaires) &&  $fraisScolaires> 0 ){
            return redirect('/frais/etude/')->with('danger','Cette Opération existe déjà');
        }else{
            FraisScolaireModel::create([
                'niveau_etude_id'=>$validated['niveau'],
                'filiere_etude_id'=>$validated['filiere'],
                'frais_inscription'=>$validated['inscription'],
                'frais_scolaire'=>$validated['frais'],
            ]);

            return redirect('/frais/etude/')->with('success','Opération effectuée avec succès');
        }
     
    }

    public function editFraisEtude($id){

        $frais_etude = FraisScolaireModel::find($id);
        $this->frais_id= $id;
        $this->filiere= $frais_etude->filiere_etude_id;
        $this->niveau= $frais_etude->niveau_etude_id;
        $this->inscription= $frais_etude->frais_inscription;
        $this->frais= $frais_etude->frais_scolaire;
    }

    public function updateFraisEtude(Request  $request)
    {
   
        $frais_etudes = FraisScolaireModel::find( $this->frais_id);
        $validated = $this->validate([
                'niveau'=>'required',
                'filiere'=>'required',
                'inscription'=>'required',
                'frais'=>'required',
        ]);

        $frais_etudes->update([
           'niveau_etude_id'=>$validated['niveau'],
            'filiere_etude_id'=>$validated['filiere'],
            'frais_inscription'=>$validated['inscription'],
            'frais_scolaire'=>$validated['frais'],
        ]);

        return redirect('/frais/etude')->with('info','Modification effectuée avec succès');
    }

}
