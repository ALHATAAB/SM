<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\DateComptableModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EtatCaisse extends Component
{
    public $caisses = [];
    public $date_comptables = [];
    public $date_comptable;
    public $caisse;
    public $countCaisse;
    public $countEntree;
    public $countSortie;
    public $montantEntree;
    public $montantSortie;
    public $solde;
    public $repport;
    public $mouvementCaisses = [];

    public function mount()
    {
        if(!empty(Permission('Tresorier')) || !empty(Permission('Parametre')))
        {
        $this->caisses = CaisseModel::all();
        }elseif(Auth::user()->id){
            $this->caisses = CaisseModel::where('user_id',Auth::user()->id)->get();
        }
        
        $this->date_comptables = DateComptableModel::all();
    }

    public function updatedCaisse($caisseId)
    {
        if (!$caisseId) {
            return redirect('etat/caisse');
        } else {
            $this->countCaisse = CaisseModel::find($caisseId)->count();
            $caisse = CaisseModel::find($caisseId);
            if ($this->countCaisse > 0) {

                $this->solde= $caisse->solde;
                $this->countEntree = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                    // ->where('date_comptable', $ $dateId)
                    ->where('sens', 'entree')
                    ->count();

                $this->montantEntree = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                    // ->where('date_comptable', $ $dateId)
                    ->where('sens', 'entree')
                    ->sum('montant'); // Récupérer la valeur et éviter les erreurs si NULL

                $this->countSortie = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                    // ->where('date_comptable', $ $dateId)
                    ->where('sens', 'sortie')
                    ->count();

                $this->montantSortie = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                    // ->where('date_comptable', $ $dateId)
                    ->where('sens', 'sortie')
                    ->sum('montant');

                $this->mouvementCaisses = MouvementCaisseModel::where('caisse_id', $caisse->id)
                    // ->where('date_comptable', $ $dateId)
                    ->orderBy('id', 'DESC')
                    ->get();
                $this->caisse = $caisse->id;
                $this->date_comptable="";
            }
        }
    }

    public function updatedDateComptable($dateId)
    {
        if (!$dateId) {
            return redirect('etat/caisse');
        } else {
            if ($this->caisse) {
                $this->countCaisse = CaisseModel::find($this->caisse)->count();
                $caisse = CaisseModel::find($this->caisse);
                if ($this->countCaisse > 0) {
                    $this->countEntree = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                        ->where('date_comptable', $dateId)
                        ->where('sens', 'entree')
                        ->count();

                    $this->montantEntree = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                        ->where('date_comptable', $dateId)
                        ->where('sens', 'entree')
                        ->sum('montant'); // Récupérer la valeur et éviter les erreurs si NULL

                    $this->countSortie = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                        ->where('date_comptable', $dateId)
                        ->where('sens', 'sortie')
                        ->count();

                    $this->montantSortie = HistoriqueOperationModel::where('caisse_id', $caisse->id)
                        ->where('date_comptable', $dateId)
                        ->where('sens', 'sortie')
                        ->sum('montant');

                    $this->mouvementCaisses = MouvementCaisseModel::where('caisse_id', $caisse->id)
                        ->where('date_comptable', $dateId)
                        ->orderBy('id', 'ASC')
                        ->get();
                    $this->caisse = $caisse->id;
                }
            } else {
                    return redirect('etat/caisse');
                }
        }
    }

    public function render()
    {
        return view('livewire.comptabilite.etat-caisse');
    }
}
