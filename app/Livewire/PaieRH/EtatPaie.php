<?php

namespace App\Livewire\PaieRH;

use App\Models\PaieRH\BulletinCalculerModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Services\BulletinPaieService;
use App\Services\CalculPaiePersonnelService;
use App\Services\DepartService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class EtatPaie extends Component
{
    public $mois_paies=[];
    public $mois_paie;
    public $etat_mois;
    public $etatsPaies,$totalPaie;
    public $totalGain,$totalRetenu,$countEtatsPaie;

    public function mount()
    {
        //verification de la caisse
        $this->mois_paies = MoisPaieModel::all();
        $this->etat_mois = MoisPaieModel::all();
        
       
    }


    public function render()
    {
        return view('livewire.paie-r-h.etat-paie');
    }

    public function paieDuMois($moisId)
    {
        if(!empty(AnneeScolaire()->id)){
        // dd($moisId,AnneeScolaire()->id);
        // $moisId=(int)$moisId;
        $this->countEtatsPaie = FichePaieModel::where('mois_id',$moisId)->where('annee_id',AnneeScolaire()->id)->count();
        // dd($this->countEtatsPaie);
        $this->etatsPaies = FichePaieModel::where('mois_id',$moisId)->where('annee_id',AnneeScolaire()->id)->get();
        $this->totalPaie = FichePaieModel::where('mois_id',$moisId)->where('annee_id',AnneeScolaire()->id)->sum('salaire_net');
        }else{
            return redirect('/etat/paie')->with('danger',"Pas d'annee scolaire actif");
        }
    }
    
    public function addCalculEtat()
    {
        $validated = $this->validate([
            'mois_paie' => 'required',
        ]);

        if (empty(AnneeScolaire()->id)) {
            return redirect('/etat/paie')->with('danger', "Pas d'annee scolaire actif");
        }

        $mois      = $validated['mois_paie'];
        $annee     = AnneeScolaire()->id;
        $reference = strtoupper(Str::random(10));

        // Modèles de bulletin du personnel, regroupés par employé.
        // CNSS, ITS et FSSP sont recalculés ici (barèmes de Paramètre paie RH, infos de l'employé),
        // pour les employés qui ont ces rubriques sur leur bulletin modèle. Leur montant enregistré
        // sur le modèle n'est qu'un aperçu : il n'est jamais repris tel quel.
        $service = app(CalculPaiePersonnelService::class);
        $modeles = ModelBulletinModel::with('rubrique')->get()->groupBy('employer_id');

        // Un employé parti avant le début de ce mois n'a plus de paie à calculer.
        $anneeScolaire = AnneeScolaire();
        $departs = 0;
        $dates = EmployerModel::whereIn('id', $modeles->keys())->pluck('date_depart', 'id');
        $modeles = $modeles->reject(function ($lignes, $employerId) use ($dates, $mois, $anneeScolaire, &$departs) {
            $parti = DepartService::moisApresDepart($dates[$employerId] ?? null, (int) $mois, $anneeScolaire);
            $departs += $parti ? 1 : 0;

            return $parti;
        });

        // Vérification AVANT toute écriture : un mois antérieur au dernier calculé ne doit pas
        // laisser des fiches créées à moitié pour une partie seulement des employés.
        foreach ($modeles->keys() as $employerId) {
            $dernierMois = FichePaieModel::where('employer_id', $employerId)->where('annee_id', $annee)->max('mois_id');
            $existe      = FichePaieModel::where('employer_id', $employerId)->where('mois_id', $mois)->where('annee_id', $annee)->exists();

            if (!$existe && $mois < $dernierMois) {
                return redirect('/etat/paie')->with('info', 'Vous avez sauté ce mois.');
            }
        }

        $crees = $misAJour = $payes = $prorates = 0;

        // Recalculer un mois est sans risque tant que la fiche n'est pas payée : les lignes du
        // bulletin (rubriques saisies + CNSS/ITS/FSSP) et les totaux sont reconstruits d'après
        // le bulletin modèle actuel de l'employé. Une fiche payée n'est jamais modifiée.
        DB::transaction(function () use ($modeles, $service, $mois, $annee, $anneeScolaire, $reference, &$crees, &$misAJour, &$payes, &$prorates) {
            foreach ($modeles as $employerId => $lignes) {
                $employe = EmployerModel::find($employerId);
                if (!$employe) {
                    continue;
                }

                // Rubriques du mois : permanentes + « ce mois seulement » de ce mois. Un employé qui n'a
                // aucune rubrique applicable ce mois-ci n'a pas de fiche à calculer.
                $lignes = $service->lignesDuMois($lignes, (int) $mois, (int) $annee);
                if ($lignes->isEmpty()) {
                    continue;
                }

                // Rubriques à formule : montants recalculés d'après les constantes actuelles
                $service->recalculerFormules($lignes);

                $fiche = FichePaieModel::where('employer_id', $employerId)
                    ->where('mois_id', $mois)
                    ->where('annee_id', $annee)
                    ->first();

                if ($fiche && $this->estPayee($fiche)) {
                    $payes++;
                    continue;
                }

                // Départ en cours de mois : paie au prorata des jours travaillés
                $ratio = DepartService::ratioTravail($employe->date_depart, (int) $mois, $anneeScolaire);
                $prorates += $ratio < 1 ? 1 : 0;

                $calcul  = $service->calculer($employe, $lignes, [], $ratio, (int) $mois, (int) $annee);
                $totaux  = [
                    'total_gain'   => $calcul['gain'],
                    'total_retenu' => $calcul['retenu'],
                    'brut_imposable'     => $calcul['brut_imposable'],
                    'brut_non_imposable' => $calcul['brut_non_imposable'],
                    'cnss_patronal' => $calcul['patronal'],
                    'anpe_patronal' => $calcul['anpe_patronal'],
                    'salaire_net'  => $calcul['net'],
                ];

                if ($fiche) {
                    $fiche->update($totaux);
                    $misAJour++;
                } else {
                    FichePaieModel::create($totaux + [
                        'employer_id' => $employerId,
                        'reference'   => $reference,
                        'statuts'     => 'EN COURS',
                        'mois_id'     => $mois,
                        'annee_id'    => $annee,
                    ]);
                    $crees++;
                }

                BulletinCalculerModel::where('employer_id', $employerId)
                    ->where('mois_id', $mois)
                    ->where('annee_id', $annee)
                    ->delete();

                // Rubriques saisies sur le bulletin modèle (hors CNSS/ITS/FSSP automatiques)...
                foreach ($lignes as $donnee) {
                    if (!empty($donnee->rubrique?->calcul_auto)) {
                        continue;
                    }

                    $gainMois = $ratio < 1 && $donnee->montant_gain !== null
                        ? round((float) $donnee->montant_gain * $ratio)
                        : $donnee->montant_gain;

                    // Répartition imposable / non imposable des gains soumis (Brute I / Brute NI)
                    $soumis = in_array($donnee->rubrique?->type_rubrique, ['Brute I', 'Brute NI'], true) && $gainMois !== null;

                    BulletinCalculerModel::create([
                        'rubrique_id'    => $donnee->rubrique_id,
                        'employer_id'    => $employerId,
                        'taux'           => $donnee->taux,
                        'nombre'         => $donnee->nombre,
                        'base'           => $donnee->base,
                        'montant_gain'   => $gainMois,
                        'montant_imposable' => $soumis ? CalculPaiePersonnelService::partImposable($donnee, (float) $gainMois) : null,
                        'montant_retenu' => $donnee->montant_retenu,
                        'montant_patronal' => $donnee->montant_patronal,
                        'mois_id'        => $mois,
                        'annee_id'       => $annee,
                    ]);
                }

                // ...puis les lignes CNSS / ITS / FSSP calculées.
                foreach ($calcul['lignes'] as $ligne) {
                    BulletinCalculerModel::create([
                        'rubrique_id'    => $ligne['rubrique_id'],
                        'employer_id'    => $employerId,
                        'taux'           => $ligne['taux'],
                        'nombre'         => null,
                        'base'           => $ligne['base'],
                        'montant_gain'   => null,
                        'montant_retenu' => $ligne['montant_retenu'],
                        'mois_id'        => $mois,
                        'annee_id'       => $annee,
                    ]);
                }

                // ...et la CNSS part patronale : ligne à part (colonne montant_patronal), ni gain ni retenue,
                // donc absente de total_gain, total_retenu et du net.
                if ($calcul['patronale']) {
                    BulletinCalculerModel::create([
                        'rubrique_id'      => $calcul['patronale']['rubrique_id'],
                        'employer_id'      => $employerId,
                        'taux'             => $calcul['patronale']['taux'],
                        'nombre'           => null,
                        'base'             => $calcul['patronale']['base'],
                        'montant_gain'     => null,
                        'montant_retenu'   => null,
                        'montant_patronal' => $calcul['patronale']['montant_patronal'],
                        'mois_id'          => $mois,
                        'annee_id'         => $annee,
                    ]);
                }

                // ...et l'ANPE part patronale : même principe, ligne à part hors net.
                if ($calcul['patronale_anpe']) {
                    BulletinCalculerModel::create([
                        'rubrique_id'      => $calcul['patronale_anpe']['rubrique_id'],
                        'employer_id'      => $employerId,
                        'taux'             => $calcul['patronale_anpe']['taux'],
                        'nombre'           => null,
                        'base'             => $calcul['patronale_anpe']['base'],
                        'montant_gain'     => null,
                        'montant_retenu'   => null,
                        'montant_patronal' => $calcul['patronale_anpe']['montant_patronal'],
                        'mois_id'          => $mois,
                        'annee_id'         => $annee,
                    ]);
                }
            }
        });

        $suffixeDeparts = ($departs ? " {$departs} employé(s) parti(s) avant ce mois : paie non calculée." : '')
            . ($prorates ? " {$prorates} paie(s) calculée(s) au prorata (départ en cours de mois)." : '');

        if ($crees || $misAJour) {
            $message = 'Opération effectuée avec succès';
            $message .= $crees ? " : {$crees} fiche(s) créée(s)" : ' :';
            $message .= $misAJour ? ($crees ? ',' : '') . " {$misAJour} fiche(s) recalculée(s)" : '';
            $message .= $payes ? " ({$payes} déjà payée(s), non modifiée(s))." : '.';
            $message .= $suffixeDeparts;

            return redirect('/etat/paie')->with('success', $message);
        }

        if (!$payes) {
            return redirect('/etat/paie')->with('warning', 'Aucune fiche à calculer pour ce mois.' . $suffixeDeparts);
        }

        return redirect('/etat/paie')->with('warning', 'Toutes les fiches de ce mois sont déjà payées : rien à recalculer.' . $suffixeDeparts);
    }

    /** Une fiche payée (règlement enregistré) ne se recalcule plus. */
    protected function estPayee(FichePaieModel $fiche): bool
    {
        return strtoupper((string) $fiche->statuts) === 'PAIE'
            || HistoriquePaiementSalaireModel::where('employer_id', $fiche->employer_id)
                ->where('mois_id', $fiche->mois_id)
                ->where('annee_id', $fiche->annee_id)
                ->exists();
    }


    public function addEditerEtat()
    {
        $validated = $this->validate([
            'mois_paie' => 'required',
        ]);
        return redirect('editer/etat/paie/'.$validated['mois_paie']);
    }

    public function addEditerBulletin()
    {
        $validated = $this->validate([
            'mois_paie' => 'required',
        ]);

        return redirect('editer/bulletin/paie/'.$validated['mois_paie']);
    }

    public function printEtatPaie()
    {
        ini_set('max_execution_time',3600);

        $validated = $this->validate([
            'mois_paie' => 'required',
        ]);

        if (empty(AnneeScolaire()->id)) {
            return redirect('/etat/paie')->with('danger',"Pas d'annee scolaire actif");
        }

        $etat = app(BulletinPaieService::class)->etatPersonnel((int) $validated['mois_paie'], (int) AnneeScolaire()->id);

        $pdf = Pdf::loadView('livewire.print.etat_paie', ['etat' => $etat])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            function() use($pdf){
                echo $pdf->output();
            },
            'etat_paie.pdf'
        );
    }

}
