<?php

namespace App\Livewire\Scolarite;

use Illuminate\Support\Facades\DB;
use App\Models\Scolarite\LivreModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class MouvementStockLivre extends Component
{
    public $livres = [];
    public $etat = [];
    public $searchTitre = '';
    public $dateDebut;
    public $dateFin;

    public function mount()
    {
        $this->loadLivres();
    }

    public function updated($property)
    {
        if (in_array($property, ['searchTitre', 'dateDebut', 'dateFin'])) {
            $this->loadLivres();
        }
    }

  public function loadLivres()
{
    $this->livres = $this->getQuery()->get()->map(function ($livre) {
        $livre->stock_reel = ($livre->total_entree ?? 0) - ($livre->total_sortie ?? 0);
        return $livre;
    });

    // Totaux pour le tableau
    $this->etat = [
        'total_entree'  => $this->livres->sum('total_entree'),
        'total_sortie'  => $this->livres->sum('total_sortie'),
        'montant_entree' => $this->livres->sum('montant_entree'),
        'montant_sortie' => $this->livres->sum('montant_sortie'),
        'stock_global'  => $this->livres->sum('stock_reel'), // <-- corrigé
    ];
}


    private function getQuery()
    {
        $query = LivreModel::query()
            ->withSum(['ligneAchats as total_entree' => function ($q) {
                $q->whereHas('achat_models', function ($q2) {
                    $q2->where('statuts', 'valider');
                });
            }], 'quantite')

            ->withSum(['ligneVentes as total_sortie' => function ($q) {
                $q->whereHas('vente_models', function ($q2) {
                    $q2->where('statuts', 'valider');
                });
            }], 'quantite')

            ->withSum(['ligneAchats as montant_entree' => function ($q) {
                $q->whereHas('achat_models', function ($q2) {
                    $q2->where('statuts', 'valider');
                });
            }], DB::raw('quantite * prix_unitaire'))

            ->withSum(['ligneVentes as montant_sortie' => function ($q) {
                $q->whereHas('vente_models', function ($q2) {
                    $q2->where('statuts', 'valider');
                });
            }], DB::raw('quantite * prix_unitaire'));

        // 🔍 filtre par titre
        if (!empty($this->searchTitre)) {
            $query->where('titre', 'like', '%' . $this->searchTitre . '%');
        }

        // 🔍 filtre par date
        if (!empty($this->dateDebut) && !empty($this->dateFin)) {
            $query->where(function ($q) {
                $q->whereHas('ligneAchats.achat_models', function ($q2) {
                    $q2->whereBetween('date_comptable', [$this->dateDebut, $this->dateFin])
                        ->where('statuts', 'valider');
                })->orWhereHas('ligneVentes.vente_models', function ($q3) {
                    $q3->whereBetween('date_comptable', [$this->dateDebut, $this->dateFin])
                        ->where('statuts', 'valider');
                });
            });
        }

        return $query;
    }


    public function exportPdf()
    {
        $livres = $this->getQuery()->get()->map(function ($livre) {
            $livre->stock_reel = ($livre->total_entree ?? 0) - ($livre->total_sortie ?? 0);
            return $livre;
        });
        // Totaux pour le tableau
        $etat = [
            'total_entree'  => $livres->sum('total_entree'),
            'total_sortie'  => $livres->sum('total_sortie'),
            'montant_entree' => $livres->sum('montant_entree'),
            'montant_sortie' => $livres->sum('montant_sortie'),
            'stock_global'  => $livres->sum('stock_actuel'),
        ];

        $pdf = Pdf::loadView('livewire.print.mouvement_stock', [
            'etat'      =>  $etat,
            'livres'      => $livres,
            'searchTitre' => $this->searchTitre,
            'dateDebut'   => $this->dateDebut,
            'dateFin'     => $this->dateFin,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'mouvement-stock-livres.pdf');
    }

    public function render()
    {
        return view('livewire.scolarite.mouvement-stock-livre');
    }
}
