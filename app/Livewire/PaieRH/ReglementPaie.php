<?php

namespace App\Livewire\PaieRH;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use App\Models\PaieRH\MoisPaieModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReglementPaie extends Component
{
    
    public $employers=[];
    public $employer_id,$matricule, $montant,$payer, $rester;
    public $nom,$prenom, $sexe,$poste,$statuts;
    public $mode_paiements=[];
    public $mois_paiements=[];
    public $mode_paiement, $coordonnee_paiement;
    public $mode_paie;
    public $mois_paiement;
    public $mois_paie;
    public $caisse=[];
    public $countCaisse;
    public $banques=[];
    public $banque;

    public function mount()
    {
        //verification de la caisse
        $this->countCaisse = CaisseModel::where('user_id',Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
        //liste des mode paiement
        $this->mode_paiements= ModePaiementModel::all();
        //liste des mois paiement
        $this->mois_paiements= MoisPaieModel::all();
        //liste des banque
        $this->banques= BanqueModel::all();
    }

    public function updatedMatricule($matriculeId){

        $employers= EmployerModel::where('matricule',$matriculeId)->first();
        // dd($employers);
        if(isset( $employers)){
            $this->employer_id=$employers->id;
            $this->nom=$employers->nom;
            $this->prenom=$employers->prenom;
            $this->poste=$employers->poste;

        }else{
            $this->nom="";
            $this->prenom="";
            $this->poste="";
        }
       
    }

     public function updatedMoisPaiement($moisPaiementId){

        $moisPaiement= FichePaieModel::where('employer_id',$this->employer_id)
                        ->where('mois_id',$moisPaiementId)->where('statuts','EN COURS')
                        ->where('annee_id',AnneeScolaire()->id)->first();
        if(isset( $moisPaiement)){
            $this->montant=$moisPaiement->salaire_net;
            $this->statuts=$moisPaiement->statuts;
        }else{
            $this->montant="";
            $this->statuts="";
        }
        
    }

    public function updatedModePaiement($modePaiementId){

        $modePaiement= ModePaiementModel::where('mode_paiement',$modePaiementId)->first();
        $this->mode_paie=$modePaiement->mode_paiement;
       
    }


    public function render()
    {
        return view('livewire.paie-r-h.reglement-paie');
    }

    public function addPaiementSalaire()
    {
        if ( $this->mode_paie == 'DEPOT' || $this->mode_paie=='TRANSFERT' || $this->mode_paie =='CHEQUE')
        {
            $validated = $this->validate([
                'matricule' => 'required',
                'montant' => 'required',
                'mode_paiement' => 'required', 
                'mois_paiement' => 'required',  
                'banque' => 'required',  
                'coordonnee_paiement'=>'required',                                
            ]);
        }else
        {
            $validated = $this->validate([
                'matricule' => 'required',
                'montant' => 'required',
                'mode_paiement' => 'required',
                'mois_paiement' => 'required',  
            ]);
        }
        $caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
        $mois_paie= MoisPaieModel::find($validated['mois_paiement']);
        $FichePaieModel=FichePaieModel::where('employer_id',$this->employer_id)
        ->where('mois_id',$validated['mois_paiement'])->where('statuts','EN COURS')
        ->where('annee_id',AnneeScolaire()->id)->first();
        
        if(!empty(AnneeScolaire()->id)){
            if ( $this->mode_paie == 'DEPOT' || $this->mode_paie=='TRANSFERT' || $this->mode_paie =='CHEQUE')
        {
            //verifier si le montant n'est pas superieur au montant banque
            $banque = BanqueModel::find($validated['banque']);
            if($banque->solde > $this->montant){
            
                //montant verserment
                $montant_payer = $this->montant;
                //libelle 
                $libelle='Paiement salaire de '.$this->nom.' '.$this->prenom.' du mois de '.$mois_paie->mois.' '.AnneeScolaire()->annee;
             
                //solde caisse
                $solde_banque=$banque->solde-$montant_payer;
                //Historique Operation
                HistoriqueOperationModel::create([
                    'caisse_id'=>$caisse->id,
                    'user_id'=>Auth::user()->id,
                    'description'=>$libelle,
                    'sens'=>"sortie",
                    'montant'=>$montant_payer,
                    'mode_paiement'=>$validated['mode_paiement'],
                    'coordonnee_paiement'=> $validated['coordonnee_paiement'],
                    'date_comptable'=>$caisse->date_comptable,
                    ]);

                    //Mouvement banque
                    MouvementBanqueModel::create([
                    'banque_id'=>$banque->id,
                    'user_id'=>Auth::user()->id,
                    'description'=>$libelle,
                    'entree'=>0,
                    'sortie'=> $montant_payer,
                    'solde'=>$solde_banque,
                    'date_comptable'=>$caisse->date_comptable,
                ]);

                //Historique paiement salaire
                HistoriquePaiementSalaireModel::create([
                    'caisse_id'=> $caisse->id,
                    'mois_id'=> $validated['mois_paiement'],
                    'annee_id'=> AnneeScolaire()->id,
                    'user_id'=>Auth::user()->id,
                    'matricule'=>$validated['matricule'],
                    'employer_id'=>$this->employer_id,
                    'salaire_payer'=> $montant_payer,
                    'mode_paiement'=>$validated['mode_paiement'],
                    'coordonnee_paiement'=> $validated['coordonnee_paiement'],
                    'date_comptable'=>$caisse->date_comptable,
                ]);
                //mise a jour FICHE DE PAIE
                $FichePaieModel->update([
                    'statuts'=> "PAIE",
                ]);
                //mise a jour solde banque
                $banque->update([
                    'solde'=> $solde_banque,
                ]);
                
              $paie_salaire = HistoriquePaiementSalaireModel::where('user_id', Auth::user()->id)->latest('id')->first();
                
               return redirect('/reglement/salaire/recu/'. encrypt( $paie_salaire->id));


            }else{
                return redirect('/reglement/paie')->with('danger',"Solde banque insuffisant");
            }
        }
        else
        {
           //verifier si le montant n'est pas superieur au montant banque
           $caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
           if($caisse->solde > $this->montant){
           
               //montant verserment
               $montant_payer = $this->montant;
               //libelle 
               $libelle='Paiement salaire de '.$this->nom.' '.$this->prenom.' du mois de '.$mois_paie->mois.' '.AnneeScolaire()->annee;
             
               //solde caisse
               $solde_caisse=$caisse->solde-$montant_payer;
               //Historique Operation
               HistoriqueOperationModel::create([
                   'caisse_id'=>$caisse->id,
                   'user_id'=>Auth::user()->id,
                   'description'=>$libelle,
                   'sens'=>"sortie",
                   'montant'=>$montant_payer,
                   'mode_paiement'=>$validated['mode_paiement'],
                   'date_comptable'=>$caisse->date_comptable,
                   ]);

                   //Mouvement caisse
                   MouvementCaisseModel::create([
                   'caisse_id'=>$caisse->id,
                   'user_id'=>Auth::user()->id,
                   'description'=>$libelle,
                   'entree'=>0,
                   'sortie'=> $montant_payer,
                   'solde'=>$solde_caisse,
                   'date_comptable'=>$caisse->date_comptable,
               ]);

               //Historique paiement salaire
               HistoriquePaiementSalaireModel::create([
                   'caisse_id'=> $caisse->id,
                   'mois_id'=> $validated['mois_paiement'],
                   'annee_id'=> AnneeScolaire()->id,
                   'user_id'=>Auth::user()->id,
                   'matricule'=>$validated['matricule'],
                   'employer_id'=>$this->employer_id,
                   'salaire_payer'=> $montant_payer,
                   'mode_paiement'=>$validated['mode_paiement'],
                   'date_comptable'=>$caisse->date_comptable,
               ]);
                //mise a jour FICHE DE PAIE
                $FichePaieModel->update([
                    'statuts'=> "PAIE",
                ]);
               //mise a jour solde caisse
               $caisse->update([
                'solde'=> $solde_caisse,
            ]);
               
            $paie_salaire = HistoriquePaiementSalaireModel::where('user_id', Auth::user()->id)->latest('id')->first();
                
               return redirect('/reglement/salaire/recu/'. encrypt( $paie_salaire->id));

           }else{
               return redirect('/reglement/paie')->with('danger',"Solde de caisse insuffisant");
           }
        }    
        }else{
            return redirect('/reglement/paie')->with('success',"Pas d'annee scolaire actif");
        }
    }


}
