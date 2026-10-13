<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\EncaissementDiversModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EncaissementDivers extends Component
{
    public $montant,$reference,$encaissementDivers,$encaissementDiversCount;
    public $caisse,$countCaisse;
    
    public function mount()
    {

        $this->countCaisse = CaisseModel::where('user_id',Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
        $this->encaissementDiversCount=EncaissementDiversModel::where('statuts',"NON AUTORISER")->count();
        $this->encaissementDivers=EncaissementDiversModel::where('statuts',"NON AUTORISER")->get();

        // if($this->countCaisse > 0){
        //     $this->countEntree = HistoriqueOperationModel::where('user_id', Auth::user()->id)
        //     ->where('caisse_id', $this->caisse->id)
        //     ->where('date_comptable', $this->caisse->date_comptable)
        //     ->where('sens', 'entree')
        //     ->count();

        //     $this->montantEntree = HistoriqueOperationModel::where('user_id', Auth::user()->id)
        //     ->where('caisse_id', $this->caisse->id)
        //     ->where('date_comptable', $this->caisse->date_comptable)
        //     ->where('sens', 'entree')
        //     ->sum('montant'); // Récupérer la valeur et éviter les erreurs si NULL

        //     $this->countSortie = HistoriqueOperationModel::where('user_id', Auth::user()->id)
        //     ->where('caisse_id', $this->caisse->id)
        //     ->where('date_comptable', $this->caisse->date_comptable)
        //     ->where('sens', 'sortie')
        //     ->count();

        //     $this->montantSortie = HistoriqueOperationModel::where('user_id', Auth::user()->id)
        //     ->where('caisse_id', $this->caisse->id)
        //     ->where('date_comptable', $this->caisse->date_comptable)
        //     ->where('sens', 'sortie')
        //     ->sum('montant');

        //     $this->mouvementCaisses=MouvementCaisseModel::where('caisse_id',$this->caisse->id)
        //     ->where('date_comptable', $this->caisse->date_comptable)
        //     ->orderBy('id','ASC')
        //     ->get();
        // }
        
        

    }

    public function render()
    {
        return view('livewire.comptabilite.encaissement-divers');
    }


    public function addEncaissementDivers()
    {
        $validated = $this->validate(
            [
                'montant'=>'required',
                'reference'=>'required',
            ]);

            $caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
            EncaissementDiversModel::create([
                'montant'=>$validated['montant'],
                'reference'=>$validated['reference'],
                'caisse_id'=>$caisse->id,
                'user_id'=>Auth::user()->id,
                'date_comptable'=>$caisse->date_comptable,
            ]);
    
            return redirect('/encaissement/divers')->with('success','Opération effectuée avec succès');
    }

    public function autoriser($id){

        $autoriser_encaissement = EncaissementDiversModel::find($id);

        //verifier si le montant n'est pas superieur au montant banque
        $caisse = CaisseModel::find($autoriser_encaissement->caisse_id);
        
    if(($caisse->etat)=="ouverte"){
        //montant verserment
        $montant_versement = $autoriser_encaissement->montant;
        //libelle 
        $libelle='Encaissement divers : '.$autoriser_encaissement->reference;
        //solde banque
        //solde caisse
        $solde_caisse=$caisse->solde+$montant_versement;
    
           //Historique Operation
            HistoriqueOperationModel::create([
                'caisse_id'=>$caisse->id,
                'user_id'=>Auth::user()->id,
                'description'=>$libelle,
                'sens'=>"entree",
                'montant'=>$montant_versement,
                'mode_paiement'=>'ESPECE',
                'coordonnee_paiement'=>$caisse->caisse,
                'date_comptable'=>$caisse->date_comptable,
                ]);

             //Mouvement caisse
             MouvementCaisseModel::create([
                'caisse_id'=>$caisse->id,
                'user_id'=>Auth::user()->id,
                'description'=>$libelle,
                'entree'=>$montant_versement,
                'sortie'=> 0 ,
                'solde'=>$solde_caisse,
                'date_comptable'=>$caisse->date_comptable,
            ]);


            //mise a jour solde caisse
            $caisse->update([
                'solde'=> $solde_caisse,
            ]);
            //mise a jour encaissement
            $autoriser_encaissement->update([
                'statuts'=>"AUTORISER",
                'date_validation'=>now(),
            ]);
            
            return redirect('/encaissement/divers')->with('success',"Opération effectuée avec succès");
        }
        else{
            return redirect('/encaissement/divers')->with('success',"Caisse Fermer.");
        }
        
    }

    public function rejeter($id){
        $rejet_encaissement = EncaissementDiversModel::find($id);
        
        //mise a jour 
        $rejet_encaissement->update([
            'statuts'=> "REJETER",
            'date_validation'=>now(),
        ]);

        return redirect('/encaissement/divers')->with('success','Opération effectuée avec succès');
    }
}
