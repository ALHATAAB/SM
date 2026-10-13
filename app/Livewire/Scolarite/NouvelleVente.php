<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\LigneVenteModel;
use App\Models\Scolarite\LivreModel;
use App\Models\Scolarite\NiveauEtudeModel;
use App\Models\Scolarite\VenteModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NouvelleVente extends Component
{
    public $client;
    public $lines = [];
     public $mode_paiements = [];
    public $mode_paiement, $coordonnee_paiement;
    public $mode_paie;
    public $caisse = [];
    public $countCaisse;
    public $banques = [];
    public $banque;
    public $remise;

    public function mount()
    {
        $this->addLine(); // une ligne vide au départ
        //verification de la caisse
        $this->countCaisse = CaisseModel::where('user_id', Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
        //liste des mode paiement
        $this->mode_paiements = ModePaiementModel::all();
        //liste des niveau etude
        // $this->niveaux = NiveauEtudeModel::all();
        // //liste des matiere
        // $this->filieres = FiliereEtudeModel::all();
        //liste des BANQUE
        $this->banques = BanqueModel::all();
    }
    public function updatedModePaiement($modePaiementId)
    {

        $modePaiement = ModePaiementModel::where('mode_paiement', $modePaiementId)->first();
        $this->mode_paie = $modePaiement->mode_paiement;

        // dd($this->mode_paie);
    }
    public function addLine()
    {
        $this->lines[] = [
            'livre_id' => null,
            'quantite' => 1,
            'prix_unitaire' => 0,
            'total_ligne' => 0,
        ];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
    }

    public function updatedLines($value, $key)
    {
        if (str_contains($key, 'livre_id')) {
            [$index,] = explode('.', $key);
            $livreId = $this->lines[$index]['livre_id'];

            if ($livreId) {
                $livre = LivreModel::find($livreId);
                if ($livre) {
                    $this->lines[$index]['prix_unitaire'] = $livre->prix_vente;
                }
            }
        }

        if (str_contains($key, 'quantite') || str_contains($key, 'prix_unitaire')) {
            [$index,] = explode('.', $key);
            $this->lines[$index]['total_ligne'] =
               (int) $this->lines[$index]['quantite'] * (float) $this->lines[$index]['prix_unitaire'];
        }
    }

    public function getTotalProperty()
    {
        return collect($this->lines)->sum(function ($line) {
            return (int) $line['quantite'] *(float) $line['prix_unitaire'];
        });
    }

    public function render()
    {
        $livres = LivreModel::all();
        return view('livewire.scolarite.nouvelle-vente', compact('livres'));
    }

     public function save()
    {
        $caisse = CaisseModel::where('user_id', Auth::user()->id)->first();

        if ($this->mode_paie == 'DEPOT' || $this->mode_paie == 'TRANSFERT' || $this->mode_paie == 'CHEQUE') {

            $this->validate([
                'client' => 'required|string',
                'lines.*.livre_id' => 'required|exists:livre_models,id',
                'lines.*.quantite' => 'required|integer|min:1',
                'lines.*.prix_unitaire' => 'required|numeric|min:0',
                'mode_paiement' => 'required',
                'coordonnee_paiement' => 'required',
                'banque' => 'required'
            ]);
        } else {
            $this->validate([
                'client' => 'required|string',
                'lines.*.livre_id' => 'required|exists:livre_models,id',
                'lines.*.quantite' => 'required|integer|min:1',
                'lines.*.prix_unitaire' => 'required|numeric|min:0',
                'mode_paiement' => 'required',
            ]);
        }

        $libelle = "Vente de livre / vendu à " .  $this->client;

        // ⚠️ Vérification des stocks disponibles
        foreach ($this->lines as $line) {
            $livre = LivreModel::find($line['livre_id']);
            if (!$livre) {
                return redirect('nouvel/vente')->with(
                    'warning',
                    'Livre introuvable.'
                );
            }

            if ($livre->stock_actuel < $line['quantite']) {
                return redirect('nouvel/vente')->with(
                    'warning',
                    "Stock insuffisant pour le livre « {$livre->titre} ». Disponible : {$livre->stock_actuel}, demandé : {$line['quantite']}."
                );
            }
        }

        if ($this->mode_paie == 'DEPOT' || $this->mode_paie == 'TRANSFERT' || $this->mode_paie == 'CHEQUE' || $this->mode_paie == 'VIREMENT') {

            $total = 0;
            foreach ($this->lines as $line) {
                $total += $line['quantite'] * $line['prix_unitaire'];
            }

            $montant_operation=$total-$this->remise;

            $vente = VenteModel::create([
                'client' => $this->client,
                'montant_total' => $montant_operation,
                'remise' => $this->remise,
                'user_id' => Auth::user()->id,
                'banque_id' => $this->banque,
                'mode_paiement' => $this->mode_paiement,
                'coordonnee_paiement' => $this->coordonnee_paiement,
                'date_comptable' =>$caisse->date_comptable,
                'annee_scolaire_id' => AnneeScolaire()->id
            ]);

            foreach ($this->lines as $line) {
                LigneVenteModel::create([
                    'vente_id' => $vente->id,
                    'livre_id' => $line['livre_id'],
                    'quantite' => $line['quantite'],
                    'prix_unitaire' => $line['prix_unitaire'],
                    'prix_total' => $line['quantite'] * $line['prix_unitaire'],
                    'date_comptable' =>$caisse->date_comptable,
                ]);

                // mise à jour du stock
                $livre = LivreModel::find($line['livre_id']);
                $livre->stock_initial = $livre->stock_actuel;
                $livre->stock_actuel -= $line['quantite'];
                $livre->save();
            }

            // Mise à jour de la banque
            $banque = BanqueModel::find($this->banque);
            $solde_banque = $banque->solde + $montant_operation;

            $banque->update([
                'solde' => $solde_banque,
            ]);

            //Mouvement BANQUE
            MouvementBanqueModel::create([
                'banque_id' => $banque->id,
                'user_id' => Auth::user()->id,
                'description' => $libelle,
                'entree' => $montant_operation,
                'sortie' => 0,
                'solde' => $solde_banque,
                'date_comptable' => $caisse->date_comptable,
            ]);

            return redirect('nouvel/vente')->with('success', 'Vente enregistrée avec succès !');

            // $post_inscription = VenteModel::where('user_id', Auth::user()->id)->latest('id')->first();
            // return redirect('/inscription/recu/' . encrypt($post_inscription->id));
        } else {

            $total = 0;
            foreach ($this->lines as $line) {
                $total += $line['quantite'] * $line['prix_unitaire'];
            }

            $montant_operation=$total-$this->remise;

            $vente = VenteModel::create([
                'client' => $this->client,
                'montant_total' => $montant_operation,
                'remise' => $this->remise,
                'user_id' => Auth::user()->id,
                'caisse_id' => $caisse->id,
                'mode_paiement' => $this->mode_paiement,
                'date_comptable' =>$caisse->date_comptable,
                'annee_scolaire_id' => AnneeScolaire()->id
            ]);

            foreach ($this->lines as $line) {
                LigneVenteModel::create([
                    'vente_id' => $vente->id,
                    'livre_id' => $line['livre_id'],
                    'quantite' => $line['quantite'],
                    'prix_unitaire' => $line['prix_unitaire'],
                    'prix_total' => $line['quantite'] * $line['prix_unitaire'],
                    'date_comptable' =>$caisse->date_comptable,
                ]);

                // mise à jour du stock
                $livre = LivreModel::find($line['livre_id']);
                $livre->stock_initial = $livre->stock_actuel;
                $livre->stock_actuel -= $line['quantite'];
                $livre->save();
            }


            //Historique Operation
            HistoriqueOperationModel::create([
                'caisse_id' => $caisse->id,
                'user_id' => Auth::user()->id,
                'description' => $libelle,
                'sens' => "entree",
                'montant' => $montant_operation,
                'mode_paiement' => $this->mode_paiement,
                'date_comptable' => $caisse->date_comptable,
            ]);

            // Mise à jour de la caisse
            $caisse = CaisseModel::find($caisse->id);
            $solde_caisse = $caisse->solde + $montant_operation;

            $caisse->update([
                'solde' => $solde_caisse,
            ]);

            //Mouvement caisse
            MouvementCaisseModel::create([
                'caisse_id' => $caisse->id,
                'user_id' => Auth::user()->id,
                'description' => $libelle,
                'entree' => $montant_operation,
                'sortie' => 0,
                'solde' => $solde_caisse,
                'date_comptable' => $caisse->date_comptable,
            ]);
            return redirect('nouvel/vente')->with('success', 'Vente enregistrée avec succès !');
            // $post_inscription = VenteModel::where('user_id', Auth::user()->id)->latest('id')->first();
            // return redirect('/inscription/recu/' . encrypt($post_inscription->id));
        }
    }
}
