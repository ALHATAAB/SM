<?php

namespace App\Livewire\Programme;

use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\InscriptionModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class ApprenantParClasse extends Component
{
    public $classes =[];
    public $classe_id;
    public $classe;
    public $listeClasses=[];
    public $listeCount;

    public function mount()
    {
        //liste des classe etude
        $this->classes = ClasseEtudeModel::orderBy('classe','ASC')->get();

    }


    public function updatedClasse($classeId){
       
        $this->classe_id=$classeId;
            $this->listeCount = InscriptionModel::where('classe_etude_id',$classeId)->where('annee_scolaire_id',AnneeScolaire()->id)
       ->count();
        $this->listeClasses = InscriptionModel::where('classe_etude_id',$classeId)->where('annee_scolaire_id',AnneeScolaire()->id)
        ->get();

        // dd( $this->coefficientMatiere );
    }

    public function render()
    {
        return view('livewire.programme.apprenant-par-classe');
    }

    public function recuPrint($id)
    {
        ini_set('max_execution_time',3600);

        // $id=decrypt($id);
        $listeClasseFirst = InscriptionModel::where('classe_etude_id',$id)->where('annee_scolaire_id',AnneeScolaire()->id)
        ->first();
        $listeClasses = InscriptionModel::where('classe_etude_id',$id)->where('annee_scolaire_id',AnneeScolaire()->id)
        ->get();

        $data = [
             'listeClasses' => $listeClasses,
             'listeClasseFirst'=> $listeClasseFirst,
        ];

        $pdf = Pdf::loadView('livewire.print.liste_classe', $data);
        return response()->streamDownload(
            function() use($pdf){
                echo $pdf->stream();
            },
            'liste-classe.pdf'
        );
    }
}
