<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Models\Scolarite\InscriptionModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HistoriquePaiementScolarite extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public function render()
    {
        //condition de recherche utilisateur
        if ($this->recherche) {
            $operation_scolarites = HistoriquePaiementScolariteModel::where("id", 'like', '%' . $this->recherche . '%')
                ->orWhere("matricule", 'like', '%' . $this->recherche . '%')    
                ->where("etat", '1')
                ->orderBy('id', 'DESC')
                ->paginate(25);
            return view('livewire.comptabilite.historique-paiement-scolarite', ['operation_scolarites' => $operation_scolarites]);
        } else {
            $operation_scolarites = HistoriquePaiementScolariteModel::where("etat", '1')->orderBy('id', 'DESC')->paginate(25);
            return view('livewire.comptabilite.historique-paiement-scolarite', ['operation_scolarites' => $operation_scolarites]);
        }
    }

    public function DeleteOperation($id)
    {

        $operation_scolarite = HistoriquePaiementScolariteModel::find($id);
        $inscription = InscriptionModel::find($operation_scolarite->inscription_id);

        if ($operation_scolarite->caisse_id != Null) {
            $caisseOperation = CaisseModel::find($operation_scolarite->caisse_id);
            //verifier si la casse est ouverte
            if ($caisseOperation->etat == 'fermer') {
                return redirect('historique/paiement/scolarite')->with('danger', 'caisse fermer');
            } elseif ($caisseOperation->solde < $operation_scolarite->frais_payer) {
                return redirect('historique/paiement/scolarite')->with('danger', 'Solde de caisse insuffisant');
            } else {
                //si les frais de scolarite sont superieur au montant saisie

                $payer = ($inscription->payer) - $operation_scolarite->frais_payer;
                $rester = $inscription->rester + $operation_scolarite->frais_payer;
                $remise = ($inscription->remise) - $operation_scolarite->remise;

                $libelle = "paiement scolarite de l'apprenant au matricule " . $operation_scolarite->matricule . " est annulé : numero operation"  . $operation_scolarite->id;

                $operation_scolarite->update([
                    'etat' => 0,
                ]);

                //mise à jour inscription
                $inscription->update([
                    'payer' => $payer,
                    'rester' => $rester,
                    'remise' => $remise,
                ]);

                //Historique Operation
                HistoriqueOperationModel::create([
                    'caisse_id' => $caisseOperation->id,
                    'user_id' => $caisseOperation->user_id,
                    'description' => $libelle,
                    'sens' => "sortie",
                    'montant' => $operation_scolarite->frais_payer,
                    'mode_paiement' => $operation_scolarite->mode_paiement,
                    'date_comptable' => $caisseOperation->date_comptable,
                ]);

                // Mise à jour de la caisse
                $solde_caisse = $caisseOperation->solde - $operation_scolarite->frais_payer;

                $caisseOperation->update([
                    'solde' => $solde_caisse,
                ]);
                //Mouvement caisse
                MouvementCaisseModel::create([
                    'caisse_id' => $caisseOperation->id,
                    'user_id' => $caisseOperation->user_id,
                    'description' => $libelle,
                    'entree' => 0,
                    'sortie' => $operation_scolarite->frais_payer,
                    'solde' => $solde_caisse,
                    'date_comptable' => $caisseOperation->date_comptable,
                ]);
                return redirect('historique/paiement/scolarite')->with('warning', 'Opération effectuée avec succès');
            }
        } else {
            $banqueOperation = BanqueModel::find($operation_scolarite->banque_id);
            //verifier si la casse est ouverte
            if ($banqueOperation->solde < $operation_scolarite->frais_payer) {
                return redirect('historique/paiement/scolarite')->with('danger', 'Solde banque insuffisant');
            } else {
                //si les frais de scolarite sont superieur au montant saisie

                $payer = ($inscription->payer) - $operation_scolarite->frais_payer;
                $rester = $inscription->rester + $operation_scolarite->frais_payer+$operation_scolarite->remise;
                $remise = ($inscription->remise) - $operation_scolarite->remise;

                $libelle = "paiement scolarite de l'apprenant au matricule " . $operation_scolarite->matricule . " est annulé : numero operation"  . $operation_scolarite->id;

                $operation_scolarite->update([
                    'etat' => 0,
                ]);

                //mise à jour inscription
                $inscription->update([
                    'payer' => $payer,
                    'rester' => $rester,
                    'remise' => $remise,
                ]);



                // Mise à jour de la caisse
                $solde_banque = $banqueOperation->solde - $operation_scolarite->frais_payer;

                $banqueOperation->update([
                    'solde' => $solde_banque,
                ]);
                //Mouvement caisse
                MouvementBanqueModel::create([
                    'banque_id' => $banqueOperation->id,
                    'user_id' =>  Auth::user()->id,
                    'description' => $libelle,
                    'entree' => 0,
                    'sortie' => $operation_scolarite->frais_payer,
                    'solde' => $solde_banque,
                    'date_comptable' => JourneeOuverte()->date_comptable,
                ]);
                return redirect('historique/paiement/scolarite')->with('warning', 'Opération effectuée avec succès');
            }
        }
    }
}
