<?php

namespace App\Livewire\PaieRH;

use App\Livewire\Concerns\VerifiePermission;
use App\Models\PaieRH\FichePaieModel;
use App\Services\BulletinPaieService;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class EditerBulletinPaie extends Component
{
    use VerifiePermission;

    public $mois_id;
    public $countEtatsPaie,$etatsPaies;

    public function mount($mois_id)
    {
        $this->mois_id=$mois_id;
        if(!empty(AnneeScolaire()->id)){
            // dd($moisId,AnneeScolaire()->id);
            // $moisId=(int)$moisId;
            $this->countEtatsPaie = FichePaieModel::where('mois_id',$this->mois_id)->where('annee_id',AnneeScolaire()->id)->count();
            // dd($this->countEtatsPaie);
            if($this->countEtatsPaie>0){
                $this->etatsPaies = FichePaieModel::where('mois_id',$this->mois_id)->where('annee_id',AnneeScolaire()->id)->get();
            }else{
                return redirect('/etat/paie')->with('danger',"Pas encore calculer");
            }
            }else{
                return redirect('/etat/paie')->with('danger',"Pas d'annee scolaire actif");
            }
    }
    
    public function render()
    {
        // Même source de données que le PDF : rubriques, base, taux, nombre, totaux et net.
        return view('livewire.paie-r-h.editer-bulletin-paie', [
            'bulletins' => $this->countEtatsPaie > 0
                ? app(BulletinPaieService::class)->personnel((int) $this->mois_id, (int) AnneeScolaire()->id)
                : [],
        ]);
    }
    public function retour()
    {
        return redirect('/etat/paie');
    }

    /**
     * Bulletins de paie de tout le personnel pour le mois (une page par employé).
     */
    public function printEtatPaie($moisId)
    {
        return $this->pdfBulletins((int) $moisId, null, 'bulletins-paie-personnel.pdf');
    }

    /**
     * Bulletin de paie d'un seul employé.
     */
    public function imprimerBulletin($ficheId)
    {
        return $this->pdfBulletins((int) $this->mois_id, (int) $ficheId, 'bulletin-paie.pdf');
    }

    protected function pdfBulletins(int $moisId, ?int $ficheId, string $fichier)
    {
        $this->exigerPermissions('Employer');

        $annee = AnneeScolaire();

        if (!$annee) {
            return redirect('/etat/paie')->with('danger', "Pas d'annee scolaire actif");
        }

        ini_set('max_execution_time', 3600);

        $service = app(BulletinPaieService::class);

        $pdf = Pdf::loadView('livewire.print.bulletin_paie_document', [
            'bulletins' => $service->personnel($moisId, (int) $annee->id, $ficheId),
            'ecole'     => $service->ecole(),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fichier);
    }
}
