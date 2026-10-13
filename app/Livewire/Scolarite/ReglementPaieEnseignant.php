<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationBanqueModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\EtatPaieEnseignantModel;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ReglementPaieEnseignant extends Component
{

     public $enseignants=[];
    public $enseignant_id,$matricule, $montant,$payer, $rester;
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
    // Retenues optionnelles : brut de la fiche, ITS cochée ou non, autres retenues / avance
    public $montant_brut = 0;
    public $appliquer_its = false;
    public $autres_retenues = '';
    public $taux_its = 5;

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
        $this->taux_its = (float) config('documents.its_enseignant', 5);
    }

    /** Retenue ITS (0 si la case n'est pas cochée). */
    protected function retenueIts(): float
    {
        return $this->appliquer_its ? round((float) $this->montant_brut * $this->taux_its / 100) : 0.0;
    }

    protected function autresRetenues(): float
    {
        return max(0.0, (float) $this->autres_retenues);
    }

    /** Net à payer = brut - ITS (si cochée) - autres retenues / avance. */
    protected function recalculerMontant(): void
    {
        if ((float) $this->montant_brut <= 0) {
            $this->montant = "";
            return;
        }
        $this->montant = max(0.0, (float) $this->montant_brut - $this->retenueIts() - $this->autresRetenues());
    }

    public function updatedAppliquerIts()
    {
        $this->recalculerMontant();
    }

    public function updatedAutresRetenues()
    {
        $this->recalculerMontant();
    }

    /** Colonnes de retenues, seulement si la migration a été exécutée. */
    protected function champsRetenues(): array
    {
        if (!Schema::hasColumn('historique_paiement_enseignant_models', 'retenue_its')) {
            return [];
        }

        return [
            'montant_brut'    => (float) $this->montant_brut,
            'retenue_its'     => $this->retenueIts(),
            'autres_retenues' => $this->autresRetenues(),
        ];
    }

     public function updatedMatricule($matriculeId){

        $enseignants= EnseignantModel::where('matricule',$matriculeId)->first();
        // dd($enseignants);
        if(isset( $enseignants)){
            $this->enseignant_id=$enseignants->id;
            $this->nom=$enseignants->nom;
            $this->prenom=$enseignants->prenom;
            $this->poste=$enseignants->poste;

        }else{
            $this->nom="";
            $this->prenom="";
            $this->poste="";
        }
       
    }

     public function updatedMoisPaiement($moisPaiementId){

        $moisPaiement= EtatPaieEnseignantModel::where('enseignant_id',$this->enseignant_id)
                        ->where('mois_id',$moisPaiementId)->where('statut','en_attente')
                        ->where('annee',AnneeScolaire()->annee)->first();
        if(isset( $moisPaiement)){
            $this->montant_brut=$moisPaiement->total_paye;
            $this->statuts=$moisPaiement->statut;
            $this->recalculerMontant();
        }else{
            $this->montant_brut=0;
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
        return view('livewire.scolarite.reglement-paie-enseignant');
    }


    public function addPaiementSalaire()
    {
        $this->validate(['autres_retenues' => 'nullable|numeric|min:0']);
        $this->recalculerMontant();
        if ((float) $this->montant_brut > 0 && (float) $this->montant <= 0) {
            $this->addError('autres_retenues', "Les retenues dépassent le total brut : le net à payer doit être positif.");
            return;
        }
        $caisse = CaisseModel::where('user_id',Auth::user()->id)->first();

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
        $mois_paie= MoisPaieModel::find($validated['mois_paiement']);
        $FichePaieModel=EtatPaieEnseignantModel::where('enseignant_id',$this->enseignant_id)
        ->where('mois_id',$validated['mois_paiement'])->where('statut','en_attente')
        ->where('annee',AnneeScolaire()->annee)->first();
        
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
                
                 HistoriqueOperationBanqueModel::create([
                'annee_scolaire_id' => AnneeScolaire()->id,
                'banque_id' =>$banque->id,
                'user_id' => Auth::id(),
                'description' => $libelle,
                'sens' => "sortie",
                'montant' => $montant_payer,
                'mode_paiement' => $validated['mode_paiement'],
                'coordonnee_paiement' => $validated['coordonnee_paiement'] ?? null,
                'date_comptable' => $caisse->date_comptable,
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
                HistoriquePaiementEnseignantModel::create([
                    'caisse_id'=> $caisse->id,
                    'mois_id'=> $validated['mois_paiement'],
                    'annee_id'=> AnneeScolaire()->id,
                    'user_id'=>Auth::user()->id,
                    'matricule'=>$validated['matricule'],
                    'enseignant_id'=>$this->enseignant_id,
                    'salaire_payer'=> $montant_payer,
                    ...$this->champsRetenues(),
                    'mode_paiement'=>$validated['mode_paiement'],
                    'coordonnee_paiement'=> $validated['coordonnee_paiement'],
                    'date_comptable'=>$caisse->date_comptable,
                    'annee_id' => AnneeScolaire()->id
                ]);
                //mise a jour FICHE DE PAIE
                $FichePaieModel->update([
                    'statut'=> "payé",
                ]);
                //mise a jour solde banque
                $banque->update([
                    'solde'=> $solde_banque,
                ]);
                
                $paie_enseignant = HistoriquePaiementEnseignantModel::where('user_id', Auth::user()->id)->latest('id')->first();
                
               return redirect('/reglement/enseignant/recu/'. encrypt( $paie_enseignant->id));
               
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
               HistoriquePaiementEnseignantModel::create([
                   'caisse_id'=> $caisse->id,
                   'mois_id'=> $validated['mois_paiement'],
                   'annee_id'=> AnneeScolaire()->id,
                   'user_id'=>Auth::user()->id,
                   'matricule'=>$validated['matricule'],
                    'enseignant_id'=>$this->enseignant_id,
                   'salaire_payer'=> $montant_payer,
                   ...$this->champsRetenues(),
                   'mode_paiement'=>$validated['mode_paiement'],
                   'date_comptable'=>$caisse->date_comptable,
                   'annee_id' => AnneeScolaire()->id
               ]);
                //mise a jour FICHE DE PAIE
                $FichePaieModel->update([
                    'statut'=> "payé",
                ]);
               //mise a jour solde caisse
               $caisse->update([
                'solde'=> $solde_caisse,
            ]);
               
             $paie_enseignant = HistoriquePaiementEnseignantModel::where('user_id', Auth::user()->id)->latest('id')->first();
                
               return redirect('/reglement/enseignant/recu/'. encrypt( $paie_enseignant->id));

           }else{
               return redirect('/reglement/paie')->with('danger',"Solde de caisse insuffisant");
           }
        }    
        }else{
            return redirect('/reglement/paie')->with('success',"Pas d'annee scolaire actif");
        }
    }
}
