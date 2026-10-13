<?php

namespace App\Livewire\Scolarite;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Request;
use App\Models\Scolarite\TrimestreModel;

class Trimestre extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $trimestre_id;
    public $trimestre;
    public $date_debut;
    public $date_fin;


    public function render()
    {
         //condition de recherche utilisateur
         if(!empty(AnneeScolaire()->id)){
         if($this->recherche){
            $trimestres = TrimestreModel::where("trimestre",'like','%'.$this->recherche.'%')->where("annee_scolaire_id",AnneeScolaire()->id)->orderBy('trimestre','DESC')
            ->paginate(5);
            return view('livewire.scolarite.trimestre',['trimestres'=>$trimestres]);
        }else{
            $trimestres = TrimestreModel::where("annee_scolaire_id",AnneeScolaire()->id)->orderBy('trimestre','DESC')->paginate(5);
            return view('livewire.scolarite.trimestre',['trimestres'=>$trimestres]);
        }
        }else{
            $trimestres = TrimestreModel::where("trimestre",$this->recherche)->orderBy('trimestre','DESC')
            ->paginate(5);
            return view('livewire.scolarite.trimestre',['trimestres'=>$trimestres]);
        }
    }

    public function addTrimestre(Request  $request)
    {
        $validated = $this->validate(
            [
                'trimestre' => 'required|unique:trimestre_models',
                'date_debut'=>'required',
                'date_fin'=>'required',
            ]
        );
        if(!empty(AnneeScolaire())){

            TrimestreModel::create([
                'trimestre'=>$validated['trimestre'],
                'date_debut'=>$validated['date_debut'],
                'date_fin'=>$validated['date_fin'],
                'annee_scolaire_id'=>AnneeScolaire()->id,
            ]);

            return redirect('/trimestre')->with('success','Opération effectuée avec succès');

        }else{
            return redirect('/trimestre')->with('danger','Aucune année scolaire actif');
        }

    }

    public function editTrimestre($id){

        $trimestre = TrimestreModel::find($id);
        $this->trimestre_id= $id;
        $this->trimestre= $trimestre->trimestre;
        $this->date_debut= $trimestre->date_debut;
        $this->date_fin= $trimestre->date_fin;
    }

    public function updateTrimestre(Request  $request)
    {
       
        $trimestres = TrimestreModel::find( $this->trimestre_id);
        $validated = $this->validate([
            'trimestre'   => 'required|unique:trimestre_models,trimestre,' . $trimestres->id,
            'date_debut'  => 'required|date',
            'date_fin'    => 'required|date|after_or_equal:date_debut',
        ]);

        $trimestres->update([
            'trimestre'=>$validated['trimestre'],
            'date_debut'=>$validated['date_debut'],
            'date_fin'=>$validated['date_fin'],
            'annee_scolaire_id'=>AnneeScolaire()->id,
        ]);
        return redirect('/trimestre')->with('info','Modification effectuée avec succès');
    }

}
