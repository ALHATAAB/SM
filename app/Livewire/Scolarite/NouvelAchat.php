<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\AchatModel;
use App\Models\Scolarite\LigneAchatModel;
use App\Models\Scolarite\LivreModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NouvelAchat extends Component
{

    public $fournisseur;
    public $lines = [];

    public function mount()
    {
        $this->addLine(); // une ligne vide au départ
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
                    $this->lines[$index]['prix_unitaire'] = $livre->prix_achat;
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
        return view('livewire.scolarite.nouvel-achat', compact('livres'));
    }


    public function save()
    {
        $this->validate([
            'fournisseur' => 'required|string',
            'lines.*.livre_id' => 'required|exists:livre_models,id',
            'lines.*.quantite' => 'required|integer|min:1',
            'lines.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $total = 0;
        foreach ($this->lines as $line) {
            $total += $line['quantite'] * $line['prix_unitaire'];
        }

        $achat = AchatModel::create([
            'fournisseur' => $this->fournisseur,
            'montant_total' => $total,
            'user_id' => Auth::user()->id,
            'date_comptable' => now(),
            'annee_scolaire_id' => AnneeScolaire()->id,
        ]);

        foreach ($this->lines as $line) {
            LigneAchatModel::create([
                'achat_id' => $achat->id,
                'livre_id' => $line['livre_id'],
                'quantite' => $line['quantite'],
                'prix_unitaire' => $line['prix_unitaire'],
                'prix_total' => $line['quantite'] * $line['prix_unitaire'],
                'date_comptable' => now(),
            ]);

            // mise à jour du stock
            $livre = LivreModel::find($line['livre_id']);
            $livre->stock_initial = $livre->stock_actuel;
            $livre->stock_actuel += $line['quantite'];
            $livre->save();
        }

        return redirect('nouvel/achat')->with('success', 'Achat enregistré avec succès !');
    }
   
}
