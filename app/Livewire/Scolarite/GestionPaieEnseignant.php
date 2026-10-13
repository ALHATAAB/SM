<?php

namespace App\Livewire\Scolarite;

use App\Livewire\Concerns\VerifiePermission;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\Scolarite\EmargementEnseignantModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\EtatPaieEnseignantModel;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use App\Services\BulletinPaieService;
use App\Services\DepartService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class GestionPaieEnseignant extends Component
{
    use VerifiePermission;

    public $mois_id;
    public $mois_paie;
    public $mois_paies = [];
    public $etat_mois = [];
    public $annee;
    public $etatsPaies,$totalPaie;
    public $countEtatsPaie;
    // Retenues optionnelles enregistrées au règlement, par enseignant : ['its' => , 'autres' => ]
    public $retenues = [];
    public $totalIts = 0, $totalAutres = 0, $totalNet = 0;

    /**
     * Retenues (ITS / autres retenues-avance) appliquées au règlement du mois, par enseignant.
     * Vides tant que l'enseignant n'est pas payé ou si la migration n'a pas été exécutée.
     */
    protected function retenuesDuMois($moisId, $anneeId): array
    {
        if (!Schema::hasColumn('historique_paiement_enseignant_models', 'retenue_its')) {
            return [];
        }

        return HistoriquePaiementEnseignantModel::where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->where('etat', 1)
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn ($h) => [$h->enseignant_id => [
                'its'    => (float) $h->retenue_its,
                'autres' => (float) $h->autres_retenues,
            ]])
            ->all();
    }

    /** Remplit retenues et totaux (brut, ITS, autres, net) pour l'affichage et l'impression. */
    protected function calculerRetenues($etatsPaies, $moisId, $anneeId): void
    {
        $this->retenues    = $this->retenuesDuMois($moisId, $anneeId);
        $this->totalIts    = collect($etatsPaies)->sum(fn ($e) => $this->retenues[$e->enseignant_id]['its'] ?? 0);
        $this->totalAutres = collect($etatsPaies)->sum(fn ($e) => $this->retenues[$e->enseignant_id]['autres'] ?? 0);
        $this->totalNet    = collect($etatsPaies)->sum('total_paye') - $this->totalIts - $this->totalAutres;
    }

    public function mount()
    {
        $this->mois_paies = MoisPaieModel::all();
        $this->etat_mois = MoisPaieModel::all();
    }

    public function addCalculPaieEnseignant()
    {
        $teachers = EnseignantModel::all();
        $this->annee = AnneeScolaire()->annee;

        $a_traiter = false;

        // « annee » (année en 4 chiffres) est aussi lue par le règlement de paie enseignant
        $avecAnnee = Schema::hasColumn('etat_paie_enseignant_models', 'annee');

        $departs = $prorates = 0;

        foreach ($teachers as $teacher) {
            // Enseignant parti avant le début de ce mois : plus de paie à calculer
            if (DepartService::moisApresDepart($teacher->date_depart, (int) $this->mois_paie, AnneeScolaire())) {
                $departs++;
                continue;
            }

            $etatExistante = EtatPaieEnseignantModel::where('enseignant_id', $teacher->id)
                ->where('mois_id', $this->mois_paie)
                ->where('annee_scolaire_id', AnneeScolaire()->id)
                ->exists();
            //si l'est n'existe pas
            if (!$etatExistante) {
                $emargementExistante = EmargementEnseignantModel::where('enseignant_id', $teacher->id)
                    ->whereMonth('date', $this->mois_paie)
                    ->where('annee_scolaire_id', AnneeScolaire()->id)
                    ->where('status', 'present')
                    ->exists();
                //si l'emargement existe
                if ($emargementExistante) {
                    $emargements = EmargementEnseignantModel::where('enseignant_id', $teacher->id)
                        ->whereMonth('date', $this->mois_paie)
                        ->where('annee_scolaire_id', AnneeScolaire()->id)
                        ->where('status', 'present')
                        ->sum('nombre_heure');

                    $heures = $emargements;
                    // dd( $heures);
                    // Salaire fixe : au prorata des jours travaillés si l'enseignant part en cours de mois.
                    // Salaire horaire : déjà exact, les émargements s'arrêtent à la date de départ.
                    $ratio = DepartService::ratioTravail($teacher->date_depart, (int) $this->mois_paie, AnneeScolaire());
                    if ($ratio < 1 && $teacher->type_contrat === 'fixe') {
                        $prorates++;
                    }
                    $montant_base = $teacher->type_contrat === 'fixe' ? $teacher->salaire_base * $ratio : 0;
                    $montant_heure = $teacher->type_contrat !== 'fixe' ? $heures * $teacher->salaire_horaire : 0;
                    $total = $montant_base + $montant_heure;

                    $donnees = [
                        'enseignant_id' => $teacher->id,
                        'mois_id' => $this->mois_paie,
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'montant_base' => round($montant_base),
                        'heures_sup' => $heures,
                        'montant_heures_sup' => round($montant_heure),
                        'total_paye' => $total,
                        'statut' => 'en_attente',
                    ];

                    if ($avecAnnee) {
                        $donnees['annee'] = AnneeScolaire()->annee;
                    }

                    EtatPaieEnseignantModel::create($donnees);

                    $a_traiter = true;
                }
            }
        }

        $suffixe = ($departs ? " {$departs} enseignant(s) parti(s) avant ce mois : paie non calculée." : '')
            . ($prorates ? " {$prorates} salaire(s) fixe(s) calculé(s) au prorata (départ en cours de mois)." : '');

        if ($a_traiter) {
            return redirect('/gestionnaire/paie/enseignant')->with('success', 'Paie générée pour les enseignants concernés.' . $suffixe);
        }

        return redirect('/gestionnaire/paie/enseignant')->with('info', "Aucune paie générée : soit déjà existante, soit aucun émargement." . $suffixe);
    }

    public function paieDuMois($moisId)
    {
        
            $this->mois_id=$moisId;
        if (!empty(AnneeScolaire()->id)) {
            $this->countEtatsPaie = EtatPaieEnseignantModel::where('mois_id', $moisId)
             ->where('annee_scolaire_id', AnneeScolaire()->id)->count();
            // dd($this->countEtatsPaie);
            $this->etatsPaies = EtatPaieEnseignantModel::where('mois_id', $moisId)
             ->where('annee_scolaire_id', AnneeScolaire()->id)->get();
        $this->totalPaie = EtatPaieEnseignantModel::where('mois_id', $moisId)
         ->where('annee_scolaire_id', AnneeScolaire()->id)->sum('total_paye');
            $this->calculerRetenues($this->etatsPaies, $moisId, AnneeScolaire()->id);
       } else {
            return redirect('/gestionnaire/paie/enseignant')->with('danger', "Pas d'annee scolaire actif");
        }
    }



    public function render()
    {
        return view('livewire.scolarite.gestion-paie-enseignant');
    }


    /**
     * Bulletin de paie d'un enseignant pour le mois affiché.
     */
    public function imprimerBulletin($etatId)
    {
        $this->exigerPermissions('Scolarite');

        return $this->pdfBulletins((int) $this->mois_id, (int) $etatId, 'bulletin-paie-enseignant.pdf');
    }

    /**
     * Bulletins de paie de tous les enseignants du mois.
     */
    public function imprimerBulletins($moisId)
    {
        $this->exigerPermissions('Scolarite');

        return $this->pdfBulletins((int) $moisId, null, 'bulletins-paie-enseignants.pdf');
    }

    protected function pdfBulletins(int $moisId, ?int $etatId, string $fichier)
    {
        $annee = AnneeScolaire();

        if (!$annee) {
            return redirect('/gestionnaire/paie/enseignant')->with('danger', "Pas d'annee scolaire actif");
        }

        ini_set('max_execution_time', 3600);

        $service = app(BulletinPaieService::class);

        $pdf = Pdf::loadView('livewire.print.bulletin_paie_document', [
            'bulletins' => $service->enseignants($moisId, (int) $annee->id, $etatId),
            'ecole'     => $service->ecole(),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fichier);
    }

    public function printEtatPaie($moisId)
    {
        ini_set('max_execution_time',3600);

        // $id=decrypt($id);
        $etatsPaies= EtatPaieEnseignantModel::where('mois_id', $moisId)
         ->where('annee_scolaire_id', AnneeScolaire()->id)->get();
        $totalPaie = EtatPaieEnseignantModel::where('mois_id', $moisId)
        ->where('annee_scolaire_id', AnneeScolaire()->id)->sum('total_paye');
       
        $this->calculerRetenues($etatsPaies, $moisId, AnneeScolaire()->id);

        $data = [
            'etatsPaies' => $etatsPaies,
            'totalPaie' => $totalPaie,
            'retenues'  => $this->retenues,
            'totalIts'  => $this->totalIts,
            'totalAutres' => $this->totalAutres,
            'totalNet'  => $this->totalNet,
        ];

        $pdf = Pdf::loadView('livewire.print.etat_paie_enseignant', $data) ->setPaper('a4', 'landscape');
        return response()->streamDownload(
            function() use($pdf){
                echo $pdf->stream();
            },
            'etat_paie.pdf'
        );

    }
}
