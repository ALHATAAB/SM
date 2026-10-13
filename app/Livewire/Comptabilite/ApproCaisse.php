<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\ApproCaisseModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ApproCaisse extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    public $caisses = [];
    public $users = [];
    public $user_id, $caisse_id, $caisse, $reference, $montant;
    public $caisse_destination_id, $caisse_destination;
    public $approCaisseCount, $countCaisse;
    public $Permission;

    public function mount()
    {
        $this->countCaisse = CaisseModel::where('user_id', Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
        if (isset($this->caisse->id)) {
            $this->caisses = CaisseModel::where('id', '!=', $this->caisse->id)->get();
        } else {
            $this->caisses = [];
        }

        if (!empty(Permission('Tresorier'))) {
            $this->approCaisseCount = ApproCaisseModel::count();
        } else {
            $this->approCaisseCount = ApproCaisseModel::where('user_id', Auth::user()->id)->count();
        }
    }
    public function render()
    {
        //condition de recherche utilisateur
        if ($this->recherche) {
            if (!empty(Permission('Tresorier'))) {
                $approCaisses = ApproCaisseModel::where("caisse_id", 'like', '%' . $this->recherche . '%')
                    ->orderBy('id', 'DESC')
                    ->paginate(5);
            } else {
                $approCaisses = ApproCaisseModel::where("caisse_id", 'like', '%' . $this->recherche . '%')
                    ->where('caisse_id', $this->caisse->id)
                    ->where('user_id', Auth::user()->id)
                    ->orderBy('id', 'DESC')
                    ->paginate(5);
            }

            return view('livewire.comptabilite.appro-caisse', ['approCaisses' => $approCaisses]);
        } else {
            if (!empty(Permission('Tresorier'))) {
                $approCaisses = ApproCaisseModel::orderBy('id', 'DESC')->paginate(5);
            } else {
                $approCaisses = ApproCaisseModel::where('user_id', Auth::user()->id)
                    ->where('caisse_id', $this->caisse->id)
                    ->orderBy('id', 'DESC')
                    ->paginate(5);
            }
            return view('livewire.comptabilite.appro-caisse', ['approCaisses' => $approCaisses]);
        }
    }

    public function addAppro()
    {
        $validated = $this->validate(
            [
                'caisse_destination_id' => 'required',
                'montant' => 'required',
                'reference' => 'required',
            ]
        );

        $caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
        if ($caisse->etat == 'ouverte') {
            if ($caisse->solde >= $validated['montant']) {

                ApproCaisseModel::create([
                    'montant' => $validated['montant'],
                    'reference' => $validated['reference'],
                    'caisse_destination_id' => $validated['caisse_destination_id'],
                    'caisse_id' => $caisse->id,
                    'user_id' => Auth::user()->id,
                    'date_comptable' => $caisse->date_comptable,
                ]);

                return redirect('/appro/caisse')->with('success', 'Opération effectuée avec succès');
            } else {
                return redirect('/appro/caisse')->with('danger', "Solde de caisse insuffisant");
            }
        } else {
            return redirect('/appro/caisse')->with('danger', "Caisse fermée");
        }
    }

    public function autoriser($id)
    {

        $appro_caisse = ApproCaisseModel::find($id);
        $caisse_source = CaisseModel::find($appro_caisse->caisse_id);
        $caisse_appro = CaisseModel::find($appro_caisse->caisse_destination_id);

        //verifier si le montant n'est pas superieur au montant caisse source
        $date_comptable = JourneeOuverte()->date_comptable;
        if ($caisse_source->etat == 'ouverte') {
            if ($caisse_appro->etat == 'ouverte') {
                if ($caisse_source->date_comptable == $date_comptable) {
                    if ($caisse_appro->date_comptable == $date_comptable) {
                        if ($caisse_source->solde >= $appro_caisse->montant) {
                            //montant approvionnent
                            $montant_appro = $appro_caisse->montant;
                            //libelle 
                            $libelle_source = 'approvisionnement caisse : (' . $appro_caisse->reference . ') destination ' . $caisse_appro->caisse;
                            $libelle_appro = 'encaissement appro : (' . $appro_caisse->reference . ') provenance ' . $caisse_source->caisse;
                            //solde caisse source
                            $solde_caisse_source = $caisse_source->solde - $montant_appro;
                            //solde caisse appro
                            $solde_caisse_appro = $caisse_appro->solde + $montant_appro;

                            //Mouvement caisse source
                            MouvementCaisseModel::create([
                                'caisse_id' => $caisse_source->id,
                                'user_id' => Auth::user()->id,
                                'description' => $libelle_source,
                                'entree' => 0,
                                'sortie' => $montant_appro,
                                'solde' => $solde_caisse_source,
                                'date_comptable' => $caisse_source->date_comptable,
                            ]);
                            //Historique Operation source
                            HistoriqueOperationModel::create([
                                'caisse_id' => $caisse_source->id,
                                'user_id' => $caisse_source->user_id,
                                'description' => $libelle_source,
                                'sens' => "sortie",
                                'montant' => $montant_appro,
                                'mode_paiement' => 'ESPECE',
                                'coordonnee_paiement' => $caisse_source->caisse,
                                'date_comptable' => $caisse_source->date_comptable,
                            ]);
                            //mise a jour solde caisse source
                            $caisse_source->update([
                                'solde' => $solde_caisse_source,
                            ]);
                            //------------------------------------------------------------------------------------
                            //Mouvement caisse appro
                            MouvementCaisseModel::create([
                                'caisse_id' => $caisse_appro->id,
                                'user_id' => $caisse_appro->user_id,
                                'description' => $libelle_appro,
                                'entree' => $montant_appro,
                                'sortie' => 0,
                                'solde' => $solde_caisse_appro,
                                'date_comptable' => $caisse_appro->date_comptable,
                            ]);

                            //Historique Operation appro
                            HistoriqueOperationModel::create([
                                'caisse_id' => $caisse_appro->id,
                                'user_id' => $caisse_appro->user_id,
                                'description' => $libelle_source,
                                'sens' => "entree",
                                'montant' => $montant_appro,
                                'mode_paiement' => 'ESPECE',
                                'coordonnee_paiement' => $caisse_source->caisse,
                                'date_comptable' => $caisse_appro->date_comptable,
                            ]);
                            //mise a jour solde caisse appro
                            $caisse_appro->update([
                                'solde' => $solde_caisse_appro,
                            ]);

                            //mise a jour depot caisse appro
                            $appro_caisse->update([
                                'statuts' => "AUTORISER",
                                'date_validation' => now(),
                            ]);

                            return redirect('/appro/caisse')->with('success', "Opération effectuée avec succès");
                        } else {
                            //caisse fermer
                            return redirect('/appro/caisse')->with('danger', "Montant caisse insuffisant");
                        }
                    }else {
                            //date caisse appro incorrect
                            return redirect('/appro/caisse')->with('danger', "La date comptable non conforme à la date caisse appro");
                        }
                } else {
                    //Date comptable non conforme a la date caisse
                    return redirect('/appro/caisse')->with('danger', "La date comptable non conforme à la date caisse source");
                }
            }else {
                            //date caisse appro incorrect
                            return redirect('/appro/caisse')->with('danger', "Caisse appro fermée");
                        }
        }else {
                            //date caisse appro incorrect
                            return redirect('/appro/caisse')->with('danger', "Caisse source fermée");
                        }
    }

    public function rejeter($id)
    {
        $appro_caisse = ApproCaisseModel::find($id);

        //mise a jour 
        $appro_caisse->update([
            'statuts' => "REJETER",
            'date_validation' => now(),
        ]);

        return redirect('/appro/caisse')->with('warning', 'Approvisionnement rejeté avec succès');
    }
}
