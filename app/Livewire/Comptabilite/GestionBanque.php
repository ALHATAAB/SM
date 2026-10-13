<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\DepotBanqueModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GestionBanque extends Component
{
    public $banques = [];
    public $caisse = [];
    public $caisses = [];
    public $historiques = [];
    public $coundHistorique, $compte_banque, $montant, $reference;
    public $countCaisse,$grandLivre;
    public $caisse_id;
    public $depotBanqueCount;
    public $depotBanques = [];

    public function mount()
    {
        //verification de la caisse
        $this->countCaisse = CaisseModel::where('user_id', Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
        $this->caisses = CaisseModel::where('etat', 'ouverte')->get();
        // $this->caisses = CaisseModel::all();
        $this->banques = BanqueModel::all();
        $this->depotBanqueCount = DepotBanqueModel::where('statuts', "!=", "REJETER")->where('statuts', "!=", "ANNULER")->count();
        $this->depotBanques = DepotBanqueModel::where('statuts', "!=", "REJETER")->where('statuts', "!=", "ANNULER")->get();
    }

    public function render()
    {
        return view('livewire.comptabilite.gestion-banque');
    }

    public function grandLivreBanque($id)
    {
        $this->coundHistorique = MouvementBanqueModel::where('banque_id', $id)->count();
        $this->grandLivre=BanqueModel::find($id);
        $this->historiques = MouvementBanqueModel::where('banque_id', $id)
            ->orderBy('id', 'DESC')->get();
    }

    public function addVersement()
    {
        $validated = $this->validate(
            [
                'compte_banque' => 'required',
                'caisse_id' => 'required',
                // 'montant_max'=>'required',
                'montant' => 'required',
                'reference' => 'required',
            ]
        );

        $caisse = CaisseModel::find($validated['caisse_id']);
        $gestionBanque = BanqueModel::find($validated['compte_banque']);
        //verifie si la caisse est ouverte
        if ($caisse->etat == 'ouverte') {
            //verifie si la date caisse est à jour
            if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) {
                //verifier si le montant n'est pas superieur au montant caisse
                if ($caisse->solde > $validated['montant']) {

                    //montant verserment
                    $montant_versement = $validated['montant'];
                    //libelle 
                    $libelle = 'Versement ' . $caisse->caisse . ' vers ' . $gestionBanque->banque . ' : ' . $validated['reference'];
                    //solde banque
                    $solde_banque = $gestionBanque->solde + $montant_versement;
                    //solde caisse
                    $solde_caisse = $caisse->solde - $montant_versement;

                    //Historique Operation
                    HistoriqueOperationModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sens' => "sortie",
                        'montant' => $montant_versement,
                        'mode_paiement' => 'ESPECE',
                        'coordonnee_paiement' => $gestionBanque->banque . '-' . $gestionBanque->numero_compte,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Mouvement caisse
                    MouvementCaisseModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sortie' => $montant_versement,
                        'entree' => 0,
                        'solde' => $solde_caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Mouvement banque
                    MouvementBanqueModel::create([
                        'banque_id' => $gestionBanque->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => $montant_versement,
                        'sortie' => 0,
                        'solde' => $solde_banque,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //mise a jour solde caisse
                    $caisse->update([
                        'solde' => $solde_caisse,
                    ]);
                    //mise a jour solde banque
                    $gestionBanque->update([
                        'solde' => $solde_banque,
                    ]);


                    return redirect('/gestion/banque')->with('success', "Opération effectuée avec succès");
                } else {
                    return redirect('/gestion/banque')->with('danger', "Solde de caisse insuffisant");
                }
            } else {
                return redirect('/gestion/banque')->with('warning', "  Regularisé la date comptable");
            }
        } else {
            return redirect('/gestion/banque')->with('warning', " caisse non ouverte");
        }
    }

    public function addRetrait()
    {
        $validated = $this->validate(
            [
                'compte_banque' => 'required',
                'caisse_id' => 'required',
                // 'montant_max'=>'required',
                'montant' => 'required',
                'reference' => 'required',
            ]
        );

        $caisse = CaisseModel::find($validated['caisse_id']);
        $gestionBanque = BanqueModel::find($validated['compte_banque']);

        //verifie si la caisse est ouverte
        if ($caisse->etat == 'ouverte') {
            //verifie si la date caisse est à jour
            if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) {


                //verifier si le montant n'est pas superieur au montant banque
                if ($gestionBanque->solde >= $validated['montant']) {

                    //montant verserment
                    $montant_versement = $validated['montant'];
                    //libelle 
                    $libelle = 'Retrait ' . $gestionBanque->banque . ' vers ' . $caisse->caisse;
                    //solde banque
                    $solde_banque = $gestionBanque->solde - $montant_versement;
                    //solde caisse
                    $solde_caisse = $caisse->solde + $montant_versement;

                    //Historique Operation
                    HistoriqueOperationModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sens' => "entree",
                        'montant' => $montant_versement,
                        'mode_paiement' => 'ESPECE',
                        'coordonnee_paiement' => $gestionBanque->banque . '-' . $gestionBanque->numero_compte,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Mouvement caisse
                    MouvementCaisseModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => $montant_versement,
                        'sortie' => 0,
                        'solde' => $solde_caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Mouvement banque
                    MouvementBanqueModel::create([
                        'banque_id' => $gestionBanque->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sortie' => $montant_versement,
                        'entree' => 0,
                        'solde' => $solde_banque,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //mise a jour solde caisse
                    $caisse->update([
                        'solde' => $solde_caisse,
                    ]);
                    //mise a jour solde banque
                    $gestionBanque->update([
                        'solde' => $solde_banque,
                    ]);

                    return redirect('/gestion/banque')->with('success', "Opération effectuée avec succès");
                } else {
                    return redirect('/gestion/banque')->with('danger', "Solde banque insuffisant");
                }
            } else {
                return redirect('/gestion/banque')->with('warning', "  Regularisé la date comptable");
            }
        } else {
            return redirect('/gestion/banque')->with('warning', " caisse non ouverte");
        }
    }

    public function addDepot()
    {

        $validated = $this->validate(
            [
                'compte_banque' => 'required',
                'montant' => 'required',
                'reference' => 'required',
            ]
        );

        DepotBanqueModel::create([
            'montant' => $validated['montant'],
            'reference' => $validated['reference'],
            'banque_id' => $validated['compte_banque'],
            'user_id' => Auth::user()->id,
            'date_comptable' => JourneeOuverte()->date_comptable,
        ]);

        return redirect('/gestion/banque')->with('success', "Opération effectuée avec succès");
    }

    public function autoriser($id)
    {

        $autoriser_depot_banque = DepotBanqueModel::find($id);

        //verifier si le montant n'est pas superieur au montant banque
        $gestionBanque = BanqueModel::find($autoriser_depot_banque->banque_id);
        $date_comptable = JourneeOuverte()->date_comptable;

        //montant verserment
        $montant_versement = $autoriser_depot_banque->montant;
        //libelle 
        $libelle = 'depot banque : ' . $autoriser_depot_banque->reference;
        //solde banque
        $solde_banque = $gestionBanque->solde + $montant_versement;

        //Mouvement banque
        MouvementBanqueModel::create([
            'banque_id' => $gestionBanque->id,
            'user_id' => Auth::user()->id,
            'description' => $libelle,
            'entree' => $montant_versement,
            'sortie' => 0,
            'solde' => $solde_banque,
            'date_comptable' => $date_comptable,
        ]);

        //mise a jour solde banque
        $gestionBanque->update([
            'solde' => $solde_banque,
        ]);

        //mise a jour depot banque
        $autoriser_depot_banque->update([
            'statuts' => "AUTORISER",
            'date_validation' => now(),
        ]);

        return redirect('/gestion/banque')->with('success', "Opération effectuée avec succès");
    }

    public function rejeter($id)
    {
        $rejet_depot_banque = DepotBanqueModel::find($id);

        //mise a jour 
        $rejet_depot_banque->update([
            'statuts' => "REJETER",
            'date_validation' => now(),
        ]);

        return redirect('/gestion/banque')->with('success', 'Opération effectuée avec succès');
    }

    public function annuler($id)
    {

        $annuler_depot_banque = DepotBanqueModel::find($id);

        //verifier si le montant n'est pas superieur au montant banque
        $gestionBanque = BanqueModel::find($annuler_depot_banque->banque_id);
        $date_comptable = JourneeOuverte()->date_comptable;

        //montant verserment
        $montant_versement = $annuler_depot_banque->montant;
        //libelle 
        $libelle = 'depot banque N°: ' . $annuler_depot_banque->id . ' de ' . $annuler_depot_banque->reference . ' est annuler';
        //solde banque
        $solde_banque = $gestionBanque->solde - $montant_versement;

        //Mouvement banque
        MouvementBanqueModel::create([
            'banque_id' => $gestionBanque->id,
            'user_id' => Auth::user()->id,
            'description' => $libelle,
            'entree' => 0,
            'sortie' => $montant_versement,
            'solde' => $solde_banque,
            'date_comptable' => $date_comptable,
        ]);

        //mise a jour solde banque
        $gestionBanque->update([
            'solde' => $solde_banque,
        ]);

        //mise a jour depot banque
        $annuler_depot_banque->update([
            'statuts' => "ANNULER",
        ]);

        return redirect('/gestion/banque')->with('warning', "Opération effectuée avec succès");
    }
}
