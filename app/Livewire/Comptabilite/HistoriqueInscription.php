<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Scolarite\HistoriqueInscriptionModel;
use App\Models\Scolarite\InscriptionModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HistoriqueInscription extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    public $classe;
    public $caisse_id;
    public $inscription_id;
    public $frais_inscription;
    public $annee_scolaire_id, $niveau_etude_id, $filiere_etude_id;
    public $matricule, $nom, $prenom, $niveau_etude, $filiere_etude;

    public function render()
    {
        //condition de recherche utilisateur
        if ($this->recherche) {
            $operation_inscriptions = HistoriqueInscriptionModel::where("matricule", 'like', '%' . $this->recherche . '%')
                ->where("etat", 1)
                ->orderBy('id', 'DESC')
                ->paginate(25);
            return view('livewire.comptabilite.historique-inscription', ['operation_inscriptions' => $operation_inscriptions]);
        } else {
            $operation_inscriptions = HistoriqueInscriptionModel::where("etat", 1)->orderBy('id', 'DESC')->paginate(25);
            return view('livewire.comptabilite.historique-inscription', ['operation_inscriptions' => $operation_inscriptions]);
        }
        return view('livewire.comptabilite.historique-inscription');
    }

    public function DeleteOperation($id)
    {
        $operation_inscription = HistoriqueInscriptionModel::find($id);

        $inscription = InscriptionModel::where('pre_inscription_id', $operation_inscription->pre_inscription_id)
            ->where('annee_scolaire_id', $operation_inscription->annee_scolaire_id)
            ->where('niveau_etude_id', $operation_inscription->niveau_etude_id)
            ->where('filiere_etude_id', $operation_inscription->filiere_etude_id)
            ->where("etat", 1)
            ->first();

        $inscriptionexiste = InscriptionModel::where('pre_inscription_id', $operation_inscription->pre_inscription_id)
            ->where('annee_scolaire_id', $operation_inscription->annee_scolaire_id)
            ->where('niveau_etude_id', $operation_inscription->niveau_etude_id)
            ->where('filiere_etude_id', $operation_inscription->filiere_etude_id)
            ->where("etat", 1)
            ->first();

        if ($inscriptionexiste) {
            if ($operation_inscription->caisse_id != Null) {
                $caisse = CaisseModel::find($operation_inscription->caisse_id);
                if ($caisse->etat == 'ouverte') {
                    if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) {
                        if ($caisse->solde >= $operation_inscription->frais_inscription) {

                            if ($inscription->payer == 0) {

                                $libelle = 'Annulation inscription de : ' . $inscription->preInscription->nom . ' ' . $inscription->preInscription->prenom . ', Matricule :' . $inscription->matricule . ', Niveau :' . $inscription->niveau->niveau . ', Option :' . $inscription->filiere->filiere;
                                HistoriqueOperationModel::create([
                                    'caisse_id' => $caisse->id,
                                    'user_id' => $caisse->user_id,
                                    'description' => $libelle,
                                    'sens' => "sortie",
                                    'montant' => $operation_inscription->frais_inscription,
                                    'mode_paiement' => "CAISSE",
                                    'date_comptable' => $caisse->date_comptable,
                                ]);

                                $caisse->update(['solde' => $caisse->solde - $operation_inscription->frais_inscription]);

                                MouvementCaisseModel::create([
                                    'caisse_id' => $caisse->id,
                                    'user_id' => $caisse->user_id,
                                    'description' => $libelle,
                                    'sortie' => $operation_inscription->frais_inscription,
                                    'entree' => 0,
                                    'solde' => $caisse->solde,
                                    'date_comptable' => $caisse->date_comptable,
                                ]);

                                $inscription->update([
                                    'etat' => 0,
                                ]);

                                $operation_inscription->update([
                                    'etat' => 0,
                                ]);;

                                return redirect('/historique/inscription')->with('info', 'Suppression effectuée avec succès');
                            } else {
                                return redirect('/historique/inscription')->with('info', "Il y'a eu des reglement de frais de scolarite !");
                            }
                        } else {
                            return redirect('/historique/inscription')->with('info', 'Solde caisse insuffisant');
                        }
                    } else {
                        return redirect('/historique/inscription')->with('info', 'Date comptable non conforme à la date caisse');
                    }
                } else {
                    return redirect('/historique/inscription')->with('info', 'Caisse fermer');
                }
            }else
            {
                 $banque = BanqueModel::find($operation_inscription->banque_id);
                  if ($banque->solde >= $operation_inscription->frais_inscription) {

                            if ($inscription->payer == 0) {

                                $libelle = 'Annulation inscription de : ' . $inscription->preInscription->nom . ' ' . $inscription->preInscription->prenom . ', Matricule :' . $inscription->matricule . ', Niveau :' . $inscription->niveau->niveau . ', Option :' . $inscription->filiere->filiere;

                                $banque->update(['solde' => $banque->solde - $operation_inscription->frais_inscription]);

                                MouvementBanqueModel::create([
                                    'banque_id' => $banque->id,
                                    'user_id' => Auth::user()->id,
                                    'description' => $libelle,
                                    'sortie' => $operation_inscription->frais_inscription,
                                    'entree' => 0,
                                    'solde' => $banque->solde,
                                    'date_comptable' => JourneeOuverte()->date_comptable,
                                ]);

                                $inscription->update([
                                    'etat' => 0,
                                ]);

                                $operation_inscription->update([
                                    'etat' => 0,
                                ]);;

                                return redirect('/historique/inscription')->with('info', 'Suppression effectuée avec succès');
                            } else {
                                return redirect('/historique/inscription')->with('info', "Il y'a eu des reglement de frais de scolarite !");
                            }
                        } else {
                            return redirect('/historique/inscription')->with('info', 'Solde banque insuffisant');
                        }
                
            }
        } else {
            return redirect('/historique/inscription')->with('info', "Cette inscription à ete modifier");
        }
    }
}
