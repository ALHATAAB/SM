<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Comptabilite\OperationDepenseModel;
use App\Models\Scolarite\TypeAbonnementModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GestionDepense extends Component
{
    public $banques = [];
    public $caisse = [];
    public $depenses = [];
    public $secteurs = [];
    public $secteur;
    public $countdepense, $compte_banque, $montant, $reference;
    public $countCaisse;

    public function mount()
    {
        //verification de la caisse
        $this->countCaisse = CaisseModel::where('user_id', Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
        $this->banques = BanqueModel::all();
        $this->secteurs = TypeAbonnementModel::all();
        if($this->caisse){
            $this->countdepense = OperationDepenseModel::where('user_id', Auth::user()->id)->where('annee_scolaire_id', AnneeScolaire()->id)->where("etat", 1)->count();
            $this->depenses = OperationDepenseModel::where('user_id', Auth::user()->id)->where('annee_scolaire_id', AnneeScolaire()->id)->where("etat", 1)->orderBy('id', 'DESC')->get();
        }else
        {
        $this->countdepense = OperationDepenseModel::where("etat", 1)->where('annee_scolaire_id', AnneeScolaire()->id)->count();
        $this->depenses = OperationDepenseModel::where("etat", 1)->where('annee_scolaire_id', AnneeScolaire()->id)->orderBy('id', 'DESC')->get();
        }
    }

    public function updatedSecteur($id) {}

    public function render()
    {
        return view('livewire.comptabilite.gestion-depense');
    }

    public function addDepenseCaisse()
    {
        $validated = $this->validate(
            [
                // 'compte_banque'=>'required',
                // 'montant_min'=>'required',
                'secteur' => 'required',
                'montant' => 'required',
                'reference' => 'required',
            ]
        );


        $caisse = CaisseModel::where('user_id', Auth::user()->id)->first();

        //verifie si la caisse est ouverte
        if ($caisse->etat == 'ouverte') {
            //verifie si la date caisse est à jour
            if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) {
                //verifier si le montant n'est pas superieur au montant banque
                if ($caisse->solde >= $validated['montant']) {

                    //montant verserment
                    $montant_depenser = $validated['montant'];
                    //libelle 
                    $libelle = $validated['reference'];
                    //solde banque
                    //solde caisse
                    $solde_caisse = $caisse->solde - $montant_depenser;

                    //Historique Operation
                    HistoriqueOperationModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sens' => "sortie",
                        'montant' => $montant_depenser,
                        'mode_paiement' => 'ESPECE',
                        'coordonnee_paiement' => $caisse->caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Mouvement caisse
                    MouvementCaisseModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => 0,
                        'sortie' => $montant_depenser,
                        'solde' => $solde_caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Operation depense
                    OperationDepenseModel::create([
                        'user_id' => Auth::user()->id,
                        'caisse_id' => $caisse->id,
                        'reference' => $libelle,
                        'type_abonnement_id' => $validated['secteur'],
                        'montant' => $montant_depenser,
                        'mode_paiement' => $caisse->caisse,
                        'date_comptable' => $caisse->date_comptable,
                        'annee_scolaire_id' => AnneeScolaire()->id
                    ]);

                    //mise a jour solde caisse
                    $caisse->update([
                        'solde' => $solde_caisse,
                    ]);


                    return redirect('/gestion/depense')->with('success', "Opération effectuée avec succès");
                } else {
                    return redirect('/gestion/depense')->with('danger', "Solde de caisse insuffisant");
                }
            } else {
                return redirect('/gestion/depense')->with('warning', "  Regularisé la date comptable");
            }
        } else {
            return redirect('/gestion/depense')->with('warning', " caisse non ouverte");
        }
    }

    public function addPaiementBanque()
    {
        $validated = $this->validate(
            [
                'secteur' => 'required',
                'compte_banque' => 'required',
                'montant' => 'required',
                'reference' => 'required',
            ]
        );

        //verifier si le montant n'est pas superieur au montant banque
        $gestionBanque = BanqueModel::find($validated['compte_banque']);
        if ($gestionBanque->solde >= $validated['montant']) {
            $date_comptable = JourneeOuverte()->date_comptable;
            //montant verserment
            $montant_depenser = $validated['montant'];
            //libelle 
            $libelle = $validated['reference'];
            //solde banque
            $solde_banque = $gestionBanque->solde - $montant_depenser;

            //Mouvement caisse
            MouvementBanqueModel::create([
                'banque_id' => $gestionBanque->id,
                'user_id' => Auth::user()->id,
                'description' => $libelle,
                'entree' => 0,
                'sortie' => $montant_depenser,
                'solde' => $solde_banque,
                'date_comptable' => $date_comptable,
            ]);

            //Operation depense
            OperationDepenseModel::create([
                'user_id' => Auth::user()->id,
                'banque_id' => $gestionBanque->id,
                'reference' => $libelle,
                'type_abonnement_id' => $validated['secteur'],
                'montant' => $montant_depenser,
                'mode_paiement' => $gestionBanque->banque,
                'date_comptable' => $date_comptable,
                'annee_scolaire_id' => AnneeScolaire()->id
            ]);

            //mise a jour solde banque
            $gestionBanque->update([
                'solde' => $solde_banque,
            ]);

            return redirect('/gestion/depense')->with('success', "Opération effectuée avec succès");
        } else {
            return redirect('/gestion/depense')->with('danger', "Solde banque insuffisant");
        }
    }


    public function annuler($id)
    {

        $annuler_depense = OperationDepenseModel::find($id);
        //libelle 
        $libelle = 'Depense N°: ' . $annuler_depense->id . ' : ' . $annuler_depense->reference . ' est annuler';

        if ($annuler_depense->caisse_id != Null) {
            $caisseOperation = CaisseModel::find($annuler_depense->caisse_id);
            //verifier si la casse est ouverte
            if ($caisseOperation->etat == 'fermer') {
                return redirect('/gestion/depense')->with('danger', 'caisse fermer');
            } elseif ($caisseOperation->date_comptable != JourneeOuverte()?->date_comptable) {
                return redirect('/gestion/depense')->with('danger', "Date comptable n'est pas conforme a la date caisse");
            } else {
                //Historique Operation
                HistoriqueOperationModel::create([
                    'caisse_id' => $caisseOperation->id,
                    'user_id' => $caisseOperation->user_id,
                    'description' => $libelle,
                    'sens' => "entree",
                    'montant' => $annuler_depense->montant,
                    'mode_paiement' => $annuler_depense->mode_paiement,
                    'date_comptable' => $caisseOperation->date_comptable,
                ]);

                // Mise à jour de la caisse
                $solde_caisse = $caisseOperation->solde + $annuler_depense->montant;

                $caisseOperation->update([
                    'solde' => $solde_caisse,
                ]);
                //Mouvement caisse
                MouvementCaisseModel::create([
                    'caisse_id' => $caisseOperation->id,
                    'user_id' => $caisseOperation->user_id,
                    'description' => $libelle,
                    'entree' => $annuler_depense->montant,
                    'sortie' => 0,
                    'solde' => $solde_caisse,
                    'date_comptable' => $caisseOperation->date_comptable,
                ]);

                //mise a jour depot banque
                $annuler_depense->update([
                    'etat' => 0,
                ]);

                return redirect('/gestion/depense')->with('warning', 'Opération effectuée avec succès');
            }
        } else {
            $banqueOperation = BanqueModel::find($annuler_depense->banque_id);
            
                // Mise à jour de la caisse
                $solde_banque = $banqueOperation->solde + $annuler_depense->montant;

                $banqueOperation->update([
                    'solde' => $solde_banque,
                ]);
                //Mouvement caisse
                MouvementBanqueModel::create([
                    'banque_id' => $banqueOperation->id,
                    'user_id' =>  Auth::user()->id,
                    'description' => $libelle,
                    'entree' => $annuler_depense->montant,
                    'sortie' =>0,
                    'solde' => $solde_banque,
                    'date_comptable' => JourneeOuverte()->date_comptable,
                ]);

                //mise a jour depot banque
                $annuler_depense->update([
                    'etat' => 0,
                ]);
                return redirect('/gestion/depense')->with('warning', 'Opération effectuée avec succès');
           
        }
    }
}
