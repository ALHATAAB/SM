<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Scolarite\AbonnementModel;
use App\Models\Scolarite\HistoriqueAbonnementModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HistoriqueAbonnement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public function render()
    {

        if ($this->recherche) {
            $operation_abonnements = HistoriqueAbonnementModel::where("matricule", 'like', '%' . $this->recherche . '%')
                ->where("etat", '1')
                ->orderBy('id', 'DESC')
                ->paginate(25);
            return view('livewire.comptabilite.historique-abonnement', ['operation_abonnements' => $operation_abonnements]);
        } else {
            $operation_abonnements = HistoriqueAbonnementModel::where("etat", '1')->orderBy('id', 'DESC')->paginate(25);
            return view('livewire.comptabilite.historique-abonnement', ['operation_abonnements' => $operation_abonnements]);
        }
    }

    public function DeleteOperation($id)
    {
        $operation_abonnement = HistoriqueAbonnementModel::find($id);

        $abonnement = AbonnementModel::where('pre_inscription_id', $operation_abonnement->pre_inscription_id)
            ->where('annee_scolaire_id', $operation_abonnement->annee_scolaire_id)
            ->where('type_abonnement_id', $operation_abonnement->type_abonnement_id)
            ->where('date_debut', $operation_abonnement->date_debut)
            ->where('date_fin', $operation_abonnement->date_fin)
            ->where('periode', $operation_abonnement->periode)
            ->where('nombre', $operation_abonnement->nombre)
            ->where('etat', 1)
            ->first();

        $abonnementexiste = AbonnementModel::where('pre_inscription_id', $operation_abonnement->pre_inscription_id)
            ->where('annee_scolaire_id', $operation_abonnement->annee_scolaire_id)
            ->where('type_abonnement_id', $operation_abonnement->type_abonnement_id)
            ->where('date_debut', $operation_abonnement->date_debut)
            ->where('date_fin', $operation_abonnement->date_fin)
            ->where('periode', $operation_abonnement->periode)
            ->where('nombre', $operation_abonnement->nombre)
            ->first();

        if ($abonnementexiste) {
            if ($operation_abonnement->caisse_id != Null) {
                $caisse = CaisseModel::find($operation_abonnement->caisse_id);
                if ($caisse->etat == 'ouverte') {
                    if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) {
                        if ($caisse->solde >= $operation_abonnement->frais_abonnement) {
                            $dateFin = Carbon::parse($abonnement->date_fin);
                            $aujourdhui = Carbon::now();
                            if (!($aujourdhui->gt($dateFin))) {

                                $libelle = 'Annulation abonnement de : ' . $abonnement->preInscription->nom . ' ' . $abonnement->preInscription->prenom . ', Matricule :' . $abonnement->matricule . ", Pour : " . $abonnement->type_abonnement->type_abonnement . " " . $abonnement->nombre . ' ' . $abonnement->periode;
                                HistoriqueOperationModel::create([
                                    'caisse_id' => $caisse->id,
                                    'user_id' => $caisse->user_id,
                                    'description' => $libelle,
                                    'sens' => "sortie",
                                    'montant' => $operation_abonnement->frais_abonnement,
                                    'mode_paiement' => "CAISSE",
                                    'date_comptable' => $caisse->date_comptable,
                                ]);

                                $caisse->update(['solde' => $caisse->solde - $operation_abonnement->frais_abonnement]);

                                MouvementCaisseModel::create([
                                    'caisse_id' => $caisse->id,
                                    'user_id' => $caisse->user_id,
                                    'description' => $libelle,
                                    'sortie' => $operation_abonnement->frais_abonnement,
                                    'entree' => 0,
                                    'solde' => $caisse->solde,
                                    'date_comptable' => $caisse->date_comptable,
                                ]);

                                $abonnement->update([
                                    'etat' => 0,
                                ]);

                                $operation_abonnement->update([
                                    'etat' => 0,
                                ]);;

                                return redirect('/historique/abonnement')->with('info', 'Suppression effectuée avec succès');
                            } else {
                                return redirect('/historique/abonnement')->with('warning', "Abonnement déja expirer !");
                            }
                        } else {
                            return redirect('/historique/abonnement')->with('warning', 'Solde caisse insuffisant');
                        }
                    } else {
                        return redirect('/historique/abonnement')->with('warning', 'Date comptable non conforme à la date caisse');
                    }
                } else {
                    return redirect('/historique/abonnement')->with('warning', 'Caisse fermer');
                }
            } else {
                $banque = BanqueModel::find($operation_abonnement->banque_id);
                if ($banque->solde >= $operation_abonnement->frais_abonnement) {

                    $dateFin = Carbon::parse($abonnement->date_fin);
                    $aujourdhui = Carbon::now();
                    if (!($aujourdhui->gt($dateFin))) {

                        $libelle = 'Annulation abonnement de : ' . $abonnement->preInscription->nom . ' ' . $abonnement->preInscription->prenom . ', Matricule :' . $abonnement->matricule . ", Pour : " . $abonnement->type_abonnement->type_abonnement . " " . $abonnement->nombre . ' ' . $abonnement->periode;

                        $banque->update(['solde' => $banque->solde - $operation_abonnement->frais_abonnement]);

                        MouvementBanqueModel::create([
                            'banque_id' => $banque->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'sortie' => $operation_abonnement->frais_abonnement,
                            'entree' => 0,
                            'solde' => $banque->solde,
                            'date_comptable' => JourneeOuverte()->date_comptable,
                        ]);

                        $abonnement->update([
                            'etat' => 0,
                        ]);

                        $operation_abonnement->update([
                            'etat' => 0,
                        ]);;

                        return redirect('/historique/abonnement')->with('info', 'Suppression effectuée avec succès');
                    } else {
                        return redirect('/historique/abonnement')->with('warning', "Abonnement déja expirer !");
                    }
                } else {
                    return redirect('/historique/abonnement')->with('warning', 'Solde banque insuffisant');
                }
            }
        } else {
            return redirect('/historique/abonnement')->with('danger', "Cet abonnement à ete supprimé");
        }
    }
}
