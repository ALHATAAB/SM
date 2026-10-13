<?php

namespace App\Livewire\PaieRH;

use App\Models\PaieRH\FichePaieModel;
use App\Services\BulletinPaieService;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class EditerEtatPaie extends Component
{
    
    public $mois_id;
    public $countEtatsPaie,$etatsPaies,$totalPaie;

    public function mount($mois_id)
    {
        $this->mois_id=$mois_id;
        if(!empty(AnneeScolaire()->id)){
            // dd($mois_id,AnneeScolaire()->id);
            // $mois_id=(int)$mois_id;
            $this->countEtatsPaie = FichePaieModel::where('mois_id',$this->mois_id)->where('annee_id',AnneeScolaire()->id)->count();
            // dd($this->countEtatsPaie);
            if($this->countEtatsPaie>0){
                $this->etatsPaies = FichePaieModel::where('mois_id',$this->mois_id)->where('annee_id',AnneeScolaire()->id)->get();
            $this->totalPaie = FichePaieModel::where('mois_id',$mois_id)->where('annee_id',AnneeScolaire()->id)->sum('salaire_net');
        
            }else{
                return redirect('/etat/paie')->with('danger',"Pas encore calculer");
            }
            }else{
                return redirect('/etat/paie')->with('danger',"Pas d'annee scolaire actif");
            }
    }
    public function render()
    {
        return view('livewire.paie-r-h.editer-etat-paie', [
            'etat' => app(BulletinPaieService::class)->etatPersonnel((int) $this->mois_id, (int) AnneeScolaire()->id),
        ]);
    }

    public function retour()
    {
        return redirect('/etat/paie');
    }

    public function printEtatPaie($mois_id)
    {
        ini_set('max_execution_time',3600);

        $etat = app(BulletinPaieService::class)->etatPersonnel((int) $mois_id, (int) AnneeScolaire()->id);

        $pdf = Pdf::loadView('livewire.print.etat_paie', ['etat' => $etat])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            function() use($pdf){
                echo $pdf->output();
            },
            'etat_paie.pdf'
        );
    }
}
