<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class GestionCaisse extends Component
{

    public $caisse=[];
    public $countCaisse;
    public $countEntree;
    public $countSortie;
    public $montantEntree;
    public $montantSortie;
    public $mouvementCaisses=[];

    public function mount()
    {

        $this->countCaisse = CaisseModel::where('user_id',Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
        $caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
       
        if($this->countCaisse > 0){
            $this->countEntree = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'entree')
            ->count();

            $this->montantEntree = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'entree')
            ->sum('montant'); // Récupérer la valeur et éviter les erreurs si NULL

            $this->countSortie = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'sortie')
            ->count();

            $this->montantSortie = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'sortie')
            ->sum('montant');

            $this->mouvementCaisses=MouvementCaisseModel::where('caisse_id',$caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->orderBy('id', 'DESC')
            ->get();
        }
        
        

    }
    public function render()
    { 
        $countCaisse = CaisseModel::where('user_id',Auth::user()->id)->count();
        
        if($countCaisse > 0){
        return view('livewire.comptabilite.gestion-caisse',
        [
            'montantEntree' => $this->montantEntree,
            'montantSortie' => $this->montantSortie // Somme de la colonne "montant"
        ]);
        }else
        {
            return view('livewire.comptabilite.gestion-caisse');
        }
    }

     public function ouvertureCaisse($id)
    {
        $caisse = CaisseModel::find($id);
       
        $caisse->update([
            'repport'=>$caisse->solde,
            'date_comptable'=>JourneeOuverte()->date_comptable,
            'etat'=>"ouverte",
        ]);
//Mouvement caisse
        MouvementCaisseModel::create([
            'caisse_id'=>$caisse->id,
            'user_id'=>Auth::user()->id,
            'description'=>"Solde repport",
            'entree'=>0,
            'sortie'=> 0 ,
            'solde'=>$caisse->solde,
            'date_comptable'=>JourneeOuverte()->date_comptable,
        ]);
         
        return redirect('gestion/caisse')->with('info','Operation effectuée avec succès');
    }

    public function clotureCaisse($id)
    {
        $caisse = CaisseModel::find( $id);
       
        $caisse->update([
            'etat'=>"fermer",
        ]);
//Mouvement caisse

        MouvementCaisseModel::create([
            'caisse_id'=>$caisse->id,
            'user_id'=>Auth::user()->id,
            'description'=>"Solde cloture",
            'entree'=>0,
            'sortie'=> 0 ,
            'solde'=>$caisse->solde,
            'date_comptable'=>$caisse->date_comptable,
        ]);
         

        
        return redirect('gestion/caisse')->with('info','Operation effectuée avec succès');
    }

    public function recuPrint()
    {
        ini_set('max_execution_time',3600);
      
        $countCaisse = CaisseModel::where('user_id',Auth::user()->id)->count();
        $caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
       

        if($countCaisse > 0){
            $countEntree = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'entree')
            ->count();

            $montantEntree = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'entree')
            ->sum('montant'); // Récupérer la valeur et éviter les erreurs si NULL

            $countSortie = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'sortie')
            ->count();

            $montantSortie = HistoriqueOperationModel::where('user_id', Auth::user()->id)
            ->where('caisse_id', $caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->where('sens', 'sortie')
            ->sum('montant');

            $mouvementCaisses=MouvementCaisseModel::where('caisse_id',$caisse->id)
            ->where('date_comptable', $caisse->date_comptable)
            ->orderBy('id', 'DESC')
            ->get();
        }


        $data = [
            'countEntree' => $countEntree,
            'montantEntree' => $montantEntree,
            'countSortie' => $countSortie,
            'montantSortie' => $montantSortie,
            'mouvementCaisses' => $mouvementCaisses,
            'caisse' => $this->caisse,
            
        ];

        $pdf = Pdf::loadView('livewire.print.gestion_caisse', $data);
        return response()->streamDownload(
            function() use($pdf){
                echo $pdf->stream();
            },
            'gestion-caisse.pdf'
        );

    }
}
