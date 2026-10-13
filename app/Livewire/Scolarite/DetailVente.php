<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Scolarite\LigneVenteModel;
use App\Models\Scolarite\LivreModel;
use App\Models\Scolarite\VenteModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailVente extends Component
{
    public $vente;

    public function mount($id)
    {
        $id = decrypt($id);

        $this->vente = VenteModel::with(['ligneVentes'])->findOrFail($id);
    }
    public function render()
    {
        return view('livewire.scolarite.detail-vente');
    }

    public function annuler()
    {
        if ($this->vente->statuts == "annuler") {
            session()->flash('error', 'Cet vente est déjà annulé.');
            return;
        }

        // session()->flash('success', 'vente annulé avec succès.');
        $caisse = CaisseModel::find($this->vente->caisse_id);
        $banque = BanqueModel::find($this->vente->banque_id);

        if ($this->vente->mode_paiement == 'DEPOT' || $this->vente->mode_paiement == 'TRANSFERT' || $this->vente->mode_paiement == 'CHEQUE' || $this->vente->mode_paiement == 'VIREMENT') {
            if ($banque->solde >= $this->vente->montant_total) {
                // ✅ Tous les stocks sont suffisants, on peut annuler
                foreach ($this->vente->ligne_vente as $ligne) {
                    $livre = $ligne->livre;

                    if ($livre) {
                        $livre->stock_actuel += $ligne->quantite;
                        $livre->save();
                    }
                }
                $total = $this->vente->montant_total;
                $libelle = "Vente numero " . $this->vente->id . " annuler";

                $this->vente->update([
                    'statuts' => "annuler",
                ]);

                // Mise à jour de la banque
                $solde_banque = $banque->solde - $total;

                $banque->update([
                    'solde' => $solde_banque,
                ]);

                //Mouvement BANQUE
                MouvementBanqueModel::create([
                    'banque_id' => $banque->id,
                    'user_id' => Auth::user()->id,
                    'description' => $libelle,
                    'entree' => 0,
                    'sortie' => $total,
                    'solde' => $solde_banque,
                    'date_comptable' => JourneeOuverte()->date_comptable,
                ]);

                $this->vente->refresh();
                // return redirect('nouvel/vente')->with('success', 'Vente enregistrée avec succès !');

                // $post_inscription = VenteModel::where('user_id', Auth::user()->id)->latest('id')->first();
                // return redirect('/inscription/recu/' . encrypt($post_inscription->id));
            } else {
                return redirect('vente/livre/detail/' . encrypt($this->vente->id))->with('warning', 'Solde banque insuffisant');
            }
        } else {

            if ($caisse->etat == 'ouverte') {
                if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) {
                    if ($caisse->solde >= $this->vente->montant_total) {
                        // ✅ Tous les stocks sont suffisants, on peut annuler
                        foreach ($this->vente->ligne_vente as $ligne) {
                            $livre = $ligne->livre;

                            if ($livre) {
                                $livre->stock_actuel += $ligne->quantite;
                                $livre->save();
                            }
                        }
                        $total = $this->vente->montant_total;
                        $libelle = "Vente numero " . $this->vente->id . " annuler";

                        $this->vente->update([
                            'statuts' => "annuler",
                        ]);
                        //Historique Operation
                        HistoriqueOperationModel::create([
                            'caisse_id' => $this->vente->caisse_id,
                            'user_id' => $caisse->user_id,
                            'description' => $libelle,
                            'sens' => "sortie",
                            'montant' => $total,
                            'mode_paiement' => "Annuler",
                            'date_comptable' => $caisse->date_comptable,
                        ]);

                        // Mise à jour de la caisse
                        $caisse = CaisseModel::find($this->vente->caisse_id);
                        $solde_caisse = $caisse->solde - $total;

                        $caisse->update([
                            'solde' => $solde_caisse,
                        ]);

                        //Mouvement caisse
                        MouvementCaisseModel::create([
                            'caisse_id' => $this->vente->caisse_id,
                            'user_id' => $caisse->user_id,
                            'description' => $libelle,
                            'entree' => 0,
                            'sortie' => $total,
                            'solde' => $solde_caisse,
                            'date_comptable' => $caisse->date_comptable,
                        ]);


                        $this->vente->refresh();
                        // return redirect('nouvel/vente')->with('success', 'Vente enregistrée avec succès !');
                        // $post_inscription = VenteModel::where('user_id', Auth::user()->id)->latest('id')->first();
                        // return redirect('/inscription/recu/' . encrypt($post_inscription->id));
                    } else {
                        return redirect('vente/livre/detail/' . encrypt($this->vente->id))->with('warning', 'Solde caisse insuffisant');
                    }
                } else {
                    return redirect('vente/livre/detail/' . encrypt($this->vente->id))->with('warning', 'Date comptable non conforme à la date caisse');
                }
            } else {
                return redirect('vente/livre/detail/' . encrypt($this->vente->id))->with('warning', 'Caisse fermer');
            }
        }
    }
}
