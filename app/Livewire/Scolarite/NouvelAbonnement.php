<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationBanqueModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\Scolarite\AbonnementModel;
use App\Models\Scolarite\FraisAbonnementModel;
use App\Models\Scolarite\HistoriqueAbonnementModel;
use App\Models\Scolarite\PreInscriptionModel;
use App\Models\Scolarite\TypeAbonnementModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NouvelAbonnement extends Component
{

    public $type_abonnements = [];
    public $apprenants = [];
    public $frais_periodiques = [];
    public $type_abonnement, $periode, $nombre, $matricule, $frais_abonnement, $frais_periodique, $remise;
    public $nom, $prenom, $sexe, $groupe, $telephone;
    public $mode_paiement, $coordonnee_paiement;
    public $date_debut, $date_fin;
    public $mode_paie;
    public $mode_paiements = [];
    public $caisses = [];
    public $caisse;
    public $banques = [];
    public $banque;
    public $countCaisse;

    public function mount()
    {
        //verification de la caisse
        $this->countCaisse = CaisseModel::where('user_id', Auth::user()->id)->count();
        $this->caisse = CaisseModel::where('user_id', Auth::user()->id)->first();

        //liste des mode paiement
        $this->mode_paiements = ModePaiementModel::all();
        //liste des classe
        $this->banques = BanqueModel::all();
        $this->type_abonnements = TypeAbonnementModel::all();
    }


    public function updatedMatricule($matriculeId)
    {
        $apprenants = PreInscriptionModel::where('matricule', $matriculeId)->first();
        if (isset($apprenants)) {
            $this->nom = $apprenants->nom;
            $this->prenom = $apprenants->prenom;
            $this->sexe = $apprenants->sexe;
            $this->groupe = $apprenants->groupe;
            $this->telephone = $apprenants->telephone;
        } else {
            $this->nom = "";
            $this->prenom = "";
            $this->sexe = "";
            $this->groupe = "";
            $this->telephone = "";
        }
    }

    public function updatedTypeAbonnement($typeabonnementId)
    {

        $this->frais_periodiques = FraisAbonnementModel::where('type_abonnement_id', $typeabonnementId)->get();
        $this->frais_periodique = "";
    }

    public function updatedFraisPeriodique($fraisperiodiqueId)
    {
        // dd($fraisperiodiqueId);
        $frais_abonnement = FraisAbonnementModel::find($fraisperiodiqueId);
        $this->frais_abonnement = $frais_abonnement->frais_abonnement;
        $this->periode = $frais_abonnement->periode;
        $this->nombre = $frais_abonnement->nombre;
    }

    public function updatedDateDebut($dateDebut)
    {

        $this->date_debut = $dateDebut;

        $date = Carbon::parse($this->date_debut);

        if ($this->periode == 'Jour(s)') {

            $this->date_fin = ($date->copy()->addDays($this->nombre)->subDay()->format('Y-m-d'));
        } else
        if ($this->periode == 'Mois') {
            $this->date_fin = $date->copy()->addMonths($this->nombre)->subDay()->format('Y-m-d');
        } else
        if ($this->periode == 'Année(s)') {
            $this->date_fin = $date->copy()->addYears($this->nombre)->subDay()->format('Y-m-d');
        } else {
            $this->date_fin = null;
        }
    }


    public function updatedModePaiement($modePaiementId)
    {
        $modePaiement = ModePaiementModel::where('mode_paiement', $modePaiementId)->first();
        $this->mode_paie = $modePaiement->mode_paiement;
        // dd($this->mode_paie);
    }



    public function render()
    {
        return view('livewire.scolarite.nouvel-abonnement');
    }

    public function addNouvelAbonnement()
    {
        
        if ($this->mode_paie == 'DEPOT' || $this->mode_paie == 'TRANSFERT' || $this->mode_paie == 'CHEQUE') {
            $validated = $this->validate([
                'matricule' => 'required',
                'type_abonnement' => 'required',
                'date_debut' => 'required',
                'date_fin' => 'required',
                'frais_abonnement' => 'required',
                'frais_periodique' => 'required',
                'mode_paiement' => 'required',
                'coordonnee_paiement' => 'required',
                'banque' => 'required',
                'remise' => '',
            ]);
        } else {
            $validated = $this->validate([
                'matricule' => 'required',
                'type_abonnement' => 'required',
                'date_debut' => 'required',
                'date_fin' => 'required',
                'frais_abonnement' => 'required',
                'frais_periodique' => 'required',
                'mode_paiement' => 'required',
                'remise' => '',
            ]);
        }

        $preInscription = PreInscriptionModel::where('matricule', $validated['matricule'])->first();

        $abonnementModel = AbonnementModel::where('pre_inscription_id', $preInscription->id)
            ->where('type_abonnement_id', $validated['type_abonnement'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                    ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']]);
            })
            ->where('periode', $this->periode)
            ->where('annee_scolaire_id', AnneeScolaire()->id)
            ->count();
        if ($abonnementModel > 0) {
            return redirect('nouvel/abonnement')->with('danger', 'Apprenant déjà abonnée');
        } else {
            if (!empty(AnneeScolaire()->id)) {
                $frais_abonnement = FraisAbonnementModel::find($validated['frais_periodique']);
                $remise = $validated['remise'];
                $libelle = "Abonnement de l'année " . AnneeScolaire()->annee . " : " . $this->nom . " " . $this->prenom . " Matricule : " . $validated['matricule'] . "; Pour : " . $frais_abonnement->type_abonnement->type_abonnement . " " . $this->nombre . ' ' . $this->periode;
                //verification frais


                if ($this->mode_paie == 'DEPOT' || $this->mode_paie == 'TRANSFERT' || $this->mode_paie == 'CHEQUE' || $this->mode_paie == 'VIREMENT') {
                     $banque = BanqueModel::find($validated['banque']);
                    //abonnement
                    AbonnementModel::create(
                        [
                            'matricule' => $validated['matricule'],
                            'pre_inscription_id' => $preInscription->id,
                            'type_abonnement_id' => $validated['type_abonnement'],
                            'periode' => $frais_abonnement->periode,
                            'nombre' => $frais_abonnement->nombre,
                            'frais_abonnement' => ($frais_abonnement->frais_abonnement - $remise),
                            'remise' => $remise,
                            'date_debut' => $validated['date_debut'],
                            'date_fin' => $validated['date_fin'],
                            'annee_scolaire_id' => AnneeScolaire()->id,
                        ]
                    );

                    //Historique abonnement
                    HistoriqueAbonnementModel::create([
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'banque_id' => $banque->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'matricule' => $validated['matricule'],
                        'pre_inscription_id' => $preInscription->id,
                        'type_abonnement_id' => $validated['type_abonnement'],
                        'periode' => $frais_abonnement->periode,
                        'nombre' => $frais_abonnement->nombre,
                        'frais_abonnement' => ($frais_abonnement->frais_abonnement - $remise),
                        'remise' => $remise,
                        'date_debut' => $validated['date_debut'],
                        'date_fin' => $validated['date_fin'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'coordonnee_paiement' => $validated['coordonnee_paiement'],
                        'date_comptable' => JourneeOuverte()->date_comptable,
                        
                    ]);

                    // Mise à jour de la banque
                    $solde_banque = $banque->solde + ($frais_abonnement->frais_abonnement - $remise);

                    $banque->update([
                        'solde' => $solde_banque,
                    ]);

                    //Mouvement BANQUE
                    MouvementBanqueModel::create([
                        'banque_id' => $banque->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => ($frais_abonnement->frais_abonnement - $remise),
                        'sortie' => 0,
                        'solde' => $solde_banque,
                        'date_comptable' => $this->caisse->date_comptable,
                    ]);

                    $poste_abonnement = HistoriqueAbonnementModel::where('user_id', Auth::user()->id)->latest('id')->first();
                    return redirect('/abonnement/recu/' . encrypt($poste_abonnement->id));
                } else {

                    $caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
                    //abonnement
                    AbonnementModel::create(
                        [
                            'matricule' => $validated['matricule'],
                            'pre_inscription_id' => $preInscription->id,
                            'type_abonnement_id' => $validated['type_abonnement'],
                            'periode' => $frais_abonnement->periode,
                            'nombre' => $frais_abonnement->nombre,
                            'frais_abonnement' => ($frais_abonnement->frais_abonnement - $remise),
                            'remise' => $remise,
                            'date_debut' => $validated['date_debut'],
                            'date_fin' => $validated['date_fin'],
                            'annee_scolaire_id' => AnneeScolaire()->id,
                        ]
                    );

                    //Historique abonnement
                    HistoriqueAbonnementModel::create([
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'matricule' => $validated['matricule'],
                        'remise' => $remise,
                        'pre_inscription_id' => $preInscription->id,
                        'type_abonnement_id' => $validated['type_abonnement'],
                        'periode' => $frais_abonnement->periode,
                        'nombre' => $frais_abonnement->nombre,
                        'frais_abonnement' => ($frais_abonnement->frais_abonnement - $remise),
                        'date_debut' => $validated['date_debut'],
                        'date_fin' => $validated['date_fin'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'date_comptable' => $caisse->date_comptable,
                    ]);


                    //Historique Operation
                    HistoriqueOperationModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sens' => "entree",
                        'montant' => ($frais_abonnement->frais_abonnement - $remise),
                        'mode_paiement' => $validated['mode_paiement'],
                        'date_comptable' => $caisse->date_comptable,
                    ]);


                    // Mise à jour de la caisse
                    $solde_caisse = $caisse->solde + (($frais_abonnement->frais_abonnement - $remise));

                    $caisse->update([
                        'solde' => $solde_caisse,
                    ]);

                    //Mouvement caisse
                    MouvementCaisseModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => ($frais_abonnement->frais_abonnement - $remise),
                        'sortie' => 0,
                        'solde' => $solde_caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    $poste_abonnement = HistoriqueAbonnementModel::where('user_id', Auth::user()->id)->latest('id')->first();
                    return redirect('/abonnement/recu/' . encrypt($poste_abonnement->id));
                }
            } else {
                return redirect('nouvel/abonnement')->with('success', "Pas d'annee scolaire actif");
            }
        }
    }
}
