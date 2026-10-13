<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReglementFraisScolaire extends Component
{
    public $niveaux = [];
    public $filieres = [];
    public $apprenants = [];
    public $niveau, $filiere;
    public $matiere, $matricule, $frais_inscription;
    public $frais_scolarite, $montant, $payer, $rester, $remise, $montant_remise;
    public $nom, $prenom, $sexe, $groupe, $telephone;
    public $inscription_id, $niveau_id, $filiere_id, $frais_scolaire;
    public $mode_paiements = [];
    public $mode_paiement, $coordonnee_paiement;
    public $mode_paie;
    public $caisse = [];
    public $countCaisse;
    public $banques = [];
    public $banque;

    public function mount()
    {
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

    public function updatedMatricule($matriculeId)
    {

        $apprenants = InscriptionModel::where('matricule', $matriculeId)
            ->where('annee_scolaire_id', AnneeScolaire()->id)
            ->where('etat', 1)
            ->first();
        // dd($apprenants);
        if (isset($apprenants)) {
            $this->matricule = $apprenants->preInscription->matricule;
            $this->nom = $apprenants->preInscription->nom;
            $this->prenom = $apprenants->preInscription->prenom;
            $this->niveaux = InscriptionModel::where('matricule', $matriculeId)
                ->where('annee_scolaire_id', AnneeScolaire()->id)->where('etat', 1)->get();
            $this->niveau = "";
        } else {
            $this->nom = "";
            $this->prenom = "";
            $this->niveau = "";
            $this->filiere = "";
        }
    }
    public function updatedNiveau($niveauId)
    {

        $this->filieres = InscriptionModel::where('niveau_etude_id', $niveauId)->where('matricule', $this->matricule)->where('etat', 1)->where('annee_scolaire_id', AnneeScolaire()->id)->get();
        $this->filiere = "";
        $this->niveau = $niveauId;
    }

    public function updatedFiliere($filiereId)
    {
        // dd($filiereId);
        $frais = InscriptionModel::where('filiere_etude_id', $filiereId)
            ->where('niveau_etude_id', $this->niveau)
            ->where('matricule', $this->matricule)
            ->where('annee_scolaire_id', AnneeScolaire()->id)->where('etat', 1)->first();

        $this->frais_scolarite = (int)($frais->frais_scolarite ?? 0);
        $this->payer =  (int)($frais->payer ?? 0);
        $this->rester = (int)($frais->rester ?? 0);
        $this->montant_remise = (int)($frais->remise ?? 0);
    }

    public function updatedModePaiement($modePaiementId)
    {

        $modePaiement = ModePaiementModel::where('mode_paiement', $modePaiementId)->first();
        $this->mode_paie = $modePaiement->mode_paiement;

        // dd($this->mode_paie);
    }
    public function render()
    {
        return view('livewire.scolarite.reglement-frais-scolaire');
    }


    public function addScolarite()
    {

        if ($this->mode_paie == 'DEPOT' || $this->mode_paie == 'TRANSFERT' || $this->mode_paie == 'CHEQUE') {
            $validated = $this->validate([
                'matricule' => 'required',
                'niveau' => 'required',
                'filiere' => 'required',
                'montant' => 'required|numeric|min:0',
                'mode_paiement' => 'required',
                'coordonnee_paiement' => 'required',
                'banque' => 'required',
                'remise' => 'nullable|numeric|min:0',
            ]);
        } else {

            $validated = $this->validate([
                'matricule' => 'required',
                'niveau' => 'required',
                'filiere' => 'required',
                'montant' => 'required|numeric|min:0',
                'mode_paiement' => 'required',
                'remise' => 'nullable|numeric|min:0',
            ]);
        }

        $inscriptionModel = InscriptionModel::where('matricule', $validated['matricule'])
            ->where('niveau_etude_id', $validated['niveau'])
            ->where('filiere_etude_id', $validated['filiere'])
            ->where('etat', 1)
            ->where('annee_scolaire_id', AnneeScolaire()->id)
            ->first();
        $remise = $this->montant_remise + $validated['remise'];
        $frais_scolarite = ($this->frais_scolarite - $remise);

        if (!empty(AnneeScolaire()->id)) {
            if ($this->mode_paie == 'DEPOT' || $this->mode_paie == 'TRANSFERT' || $this->mode_paie == 'CHEQUE') {

                $banque = BanqueModel::find($validated['banque']);
                //si les frais de scolarite sont superieur au montant saisie
                if ($frais_scolarite > 0 && $frais_scolarite >= $validated['montant']) {
                    //montant payer plus le montant saisie
                    $payer = $inscriptionModel->payer + $validated['montant'];
                    //les frais moin le montant payer
                    $rester = $frais_scolarite - $payer;
                    $libelle = "Paiement scolarite de l'année " . AnneeScolaire()->annee . " Categorie : " . $inscriptionModel->niveau->niveau . " Option : " . $inscriptionModel->filiere->filiere . " : " . $this->nom . " " . $this->prenom . "  Matricule : " . $validated['matricule'];
                    //si le montant payer est superieur ou egale au frais
                    if ($payer >= $frais_scolarite) {
                        $payer = $frais_scolarite;
                        $rester = 0;
                        $montantOperation = $this->rester;
                        //mise a jour frais scolarite 
                        $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                        $paiement_frais->update([
                            'payer' => $payer,
                            'rester' => $rester,
                            'remise' => $remise,
                        ]);

                        //Historique paiement scolarite

                        HistoriquePaiementScolariteModel::create([
                            'annee_scolaire_id' => AnneeScolaire()->id,
                            'banque_id' => $banque->id,
                            'user_id' => Auth::user()->id,
                            'inscription_id' => $inscriptionModel->id,
                            'description' => $libelle,
                            'matricule' => $validated['matricule'],
                            'frais_payer' =>$validated['montant'],
                            'mode_paiement' => $validated['mode_paiement'],
                            'coordonnee_paiement' => $validated['coordonnee_paiement'],
                            'date_comptable' => JourneeOuverte()->date_comptable,
                        ]);


                        // Mise à jour de la banque

                        $solde_banque = $banque->solde +$validated['montant'];

                        $banque->update([
                            'solde' => $solde_banque,
                        ]);
                        //Mouvement caisse
                        MouvementBanqueModel::create([
                            'banque_id' => $banque->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'entree' => $validated['montant'],
                            'sortie' => 0,
                            'solde' => $solde_banque,
                            'date_comptable' => JourneeOuverte()->date_comptable,
                        ]);

                        $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->where('etat', 1)->latest('id')->first();
                        return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                    } else {
                        //mise a jour frais scolarite 
                        $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                        $paiement_frais->update([
                            'payer' => $payer,
                            'rester' => $rester,
                            'remise' => $remise,
                        ]);
                        //Historique paiement scolarite
                        HistoriquePaiementScolariteModel::create([
                            'annee_scolaire_id' => AnneeScolaire()->id,
                            'banque_id' => $banque->id,
                            'inscription_id' => $inscriptionModel->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'matricule' => $validated['matricule'],
                            'frais_payer' => $validated['montant'],
                            'remise' => $validated['remise'],
                            'mode_paiement' => $validated['mode_paiement'],
                            'coordonnee_paiement' => $validated['coordonnee_paiement'],
                            'date_comptable' => JourneeOuverte()->date_comptable,
                        ]);

                        // Mise à jour de la banque

                        $solde_banque = $banque->solde + $validated['montant'];

                        $banque->update([
                            'solde' => $solde_banque,
                        ]);
                        //Mouvement banque

                        MouvementBanqueModel::create([
                            'banque_id' => $banque->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'entree' => $validated['montant'],
                            'sortie' => 0,
                            'solde' => $solde_banque,
                            'date_comptable' => JourneeOuverte()->date_comptable,
                        ]);

                        $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->latest('id')->first();
                        return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                    }
                } else if ($frais_scolarite > 0 && $frais_scolarite == $validated['montant']) {
                    $payer = $validated['montant'];
                    $rester = $frais_scolarite - $validated['montant'];
                    $libelle = "Paiement scolarite de l'année " . AnneeScolaire()->annee . " Categorie : " . $inscriptionModel->niveau->niveau . " Option : " . $inscriptionModel->filiere->filiere . " : " . $this->nom . " " . $this->prenom . "  Matricule : " . $validated['matricule'];

                    //mise a jour frais scolarite 
                    $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                    $paiement_frais->update([
                        'payer' => $payer,
                        'rester' => $rester,
                        'remise' => $remise,
                    ]);

                    //Historique paiement scolarite

                    HistoriquePaiementScolariteModel::create([
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'banque_id' => $banque->id,
                        'inscription_id' => $inscriptionModel->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'matricule' => $validated['matricule'],
                        'frais_payer' => $validated['montant'],
                        'remise' => $validated['remise'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'coordonnee_paiement' => $validated['coordonnee_paiement'],
                        'date_comptable' => JourneeOuverte()->date_comptable,
                    ]);

                    // Mise à jour de la banque
                    $solde_banque = $banque->solde + $validated['montant'];

                    $banque->update([
                        'solde' => $solde_banque,
                    ]);
                    //Mouvement banque

                    MouvementBanqueModel::create([
                        'banque_id' => $banque->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => $validated['montant'],
                        'sortie' => 0,
                        'solde' => $solde_banque,
                        'date_comptable' => JourneeOuverte()->date_comptable,
                    ]);

                    $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->where('etat', 1)->latest('id')->first();
                    return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                } else if ($frais_scolarite > 0 && $frais_scolarite <= $validated['montant']) {
                    $payer = $this->frais_scolarite - $remise;
                    $rester = 0;
                    $libelle = "Paiement scolarite de l'année " . AnneeScolaire()->annee . " Categorie : " . $inscriptionModel->niveau->niveau . " Option : " . $inscriptionModel->filiere->filiere . " : " . $this->nom . " " . $this->prenom . "  Matricule : " . $validated['matricule'];

                    //mise a jour frais scolarite 
                    $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                    $paiement_frais->update([
                        'payer' => $payer,
                        'rester' => $rester,
                        'remise' => $remise,
                    ]);


                    //Historique paiement scolarite

                    HistoriquePaiementScolariteModel::create([
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'banque_id' => $banque->id,
                        'inscription_id' => $inscriptionModel->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'matricule' => $validated['matricule'],
                        'frais_payer' => $validated['montant'],
                        'remise' => $validated['remise'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'coordonnee_paiement' => $validated['coordonnee_paiement'],
                        'date_comptable' => JourneeOuverte()->date_comptable,
                    ]);
                    // Mise à jour de la banque
                    $solde_banque = $banque->solde + $validated['montant'];

                    $banque->update([
                        'solde' => $solde_banque,
                    ]);
                    //Mouvement caisse
                    MouvementBanqueModel::create([
                        'banque_id' => $banque->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => $validated['montant'],
                        'sortie' => 0,
                        'solde' => $solde_banque,
                        'date_comptable' => JourneeOuverte()->date_comptable,
                    ]);

                    $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->where('etat', 1)->latest('id')->first();
                    return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                }
            } else {

                $caisse = CaisseModel::where('user_id', Auth::user()->id)->first();
                //si les frais de scolarite sont superieur au montant saisie
                if ($frais_scolarite > 0 && $frais_scolarite >= $validated['montant']) {

                    //montant payer plus le montant saisie
                    $payer = $inscriptionModel->payer + $validated['montant'];
                    //les frais moin le montant payer
                    $rester = ($this->frais_scolarite - $remise) - $payer;

                    $libelle = "Paiement scolarite de l'année " . AnneeScolaire()->annee . " Categorie : " . $inscriptionModel->niveau->niveau . " Option : " . $inscriptionModel->filiere->filiere . " : " . $this->nom . " " . $this->prenom . "  Matricule : " . $validated['matricule'];
                    //si le montant payer est superieur ou egale au frais
                    if ($payer >= $frais_scolarite) {
                        $payer = $frais_scolarite;
                        $rester = 0;
                        $montantOperation = $this->rester;
                        //mise a jour frais scolarite 
                        $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                        $paiement_frais->update([
                            'payer' => $payer,
                            'rester' => $rester,
                            'remise' => $remise,
                        ]);

                        //Historique paiement scolarite

                        HistoriquePaiementScolariteModel::create([
                            'annee_scolaire_id' => AnneeScolaire()->id,
                            'caisse_id' => $caisse->id,
                            'user_id' => Auth::user()->id,
                            'inscription_id' => $inscriptionModel->id,
                            'description' => $libelle,
                            'matricule' => $validated['matricule'],
                            'frais_payer' => $validated['montant'],
                            'remise' => $validated['remise'],
                            'mode_paiement' => $validated['mode_paiement'],
                            'date_comptable' => $caisse->date_comptable,
                        ]);

                        //Historique Operation
                        HistoriqueOperationModel::create([
                            'caisse_id' => $caisse->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'sens' => "entree",
                            'montant' =>$validated['montant'],
                            'mode_paiement' => $validated['mode_paiement'],
                            'date_comptable' => $caisse->date_comptable,
                        ]);
                        // Mise à jour de la caisse

                        $caisse = CaisseModel::find($caisse->id);
                        $solde_caisse = $caisse->solde + $validated['montant'];

                        $caisse->update([
                            'solde' => $solde_caisse,
                        ]);
                        //Mouvement caisse

                        MouvementCaisseModel::create([
                            'caisse_id' => $caisse->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'entree' => $validated['montant'],
                            'sortie' => 0,
                            'solde' => $solde_caisse,
                            'date_comptable' => $caisse->date_comptable,
                        ]);

                        $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->where('etat', 1)->latest('id')->first();
                        return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                    } else {
                        //mise a jour frais scolarite 
                        $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                        $paiement_frais->update([
                            'payer' => $payer,
                            'rester' => $rester,
                            'remise' => $remise,
                        ]);

                        //Historique paiement scolarite

                        HistoriquePaiementScolariteModel::create([
                            'annee_scolaire_id' => AnneeScolaire()->id,
                            'caisse_id' => $caisse->id,
                            'inscription_id' => $inscriptionModel->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'matricule' => $validated['matricule'],
                            'frais_payer' => $validated['montant'],
                            'remise' => $validated['remise'],
                            'mode_paiement' => $validated['mode_paiement'],
                            'date_comptable' => $caisse->date_comptable,
                        ]);

                        //Historique Operation
                        HistoriqueOperationModel::create([
                            'caisse_id' => $caisse->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'sens' => "entree",
                            'montant' => $validated['montant'],
                            'mode_paiement' => $validated['mode_paiement'],
                            'date_comptable' => $caisse->date_comptable,
                        ]);
                        // Mise à jour de la caisse

                        $caisse = CaisseModel::find($caisse->id);
                        $solde_caisse = $caisse->solde + $validated['montant'];

                        $caisse->update([
                            'solde' => $solde_caisse,
                        ]);
                        //Mouvement caisse

                        MouvementCaisseModel::create([
                            'caisse_id' => $caisse->id,
                            'user_id' => Auth::user()->id,
                            'description' => $libelle,
                            'entree' => $validated['montant'],
                            'sortie' => 0,
                            'solde' => $solde_caisse,
                            'date_comptable' => $caisse->date_comptable,
                        ]);

                        $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->latest('id')->first();
                        return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                    }
                } else if ($frais_scolarite > 0 && $frais_scolarite == $validated['montant']) {
                    $payer = $validated['montant'];
                    $rester = $frais_scolarite - $validated['montant'];

                    $libelle = "Paiement scolarite de l'année " . AnneeScolaire()->annee . " Categorie : " . $inscriptionModel->niveau->niveau . " Option : " . $inscriptionModel->filiere->filiere . " : " . $this->nom . " " . $this->prenom . "  Matricule : " . $validated['matricule'];

                    //mise a jour frais scolarite 
                    $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                    $paiement_frais->update([
                        'payer' => $payer,
                        'rester' => $rester,
                        'remise' => $remise,
                    ]);

                    //Historique paiement scolarite

                    HistoriquePaiementScolariteModel::create([
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'caisse_id' => $caisse->id,
                        'inscription_id' => $inscriptionModel->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'matricule' => $validated['matricule'],
                        'frais_payer' => $validated['montant'],
                        'remise' => $validated['remise'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Historique Operation
                    HistoriqueOperationModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sens' => "entree",
                        'montant' => $validated['montant'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'date_comptable' => $caisse->date_comptable,
                    ]);
                    // Mise à jour de la caisse

                    $caisse = CaisseModel::find($caisse->id);
                    $solde_caisse = $caisse->solde + $validated['montant'];

                    $caisse->update([
                        'solde' => $solde_caisse,
                    ]);
                    //Mouvement caisse

                    MouvementCaisseModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => $validated['montant'],
                        'sortie' => 0,
                        'solde' => $solde_caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->where('etat', 1)->latest('id')->first();
                    return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                } else if ($frais_scolarite > 0 && $frais_scolarite  <= $validated['montant']) {
                    $payer = $frais_scolarite;
                    $rester = 0;
                    $libelle = "paiement scolarite de " . AnneeScolaire()->annee . " : " . $this->nom . " " . $this->prenom . " Matricule : " . $validated['matricule'];

                    //mise a jour frais scolarite 
                    $paiement_frais = InscriptionModel::find($inscriptionModel->id);

                    $paiement_frais->update([
                        'payer' => $payer,
                        'rester' => $rester,
                        'remise' => $remise,
                    ]);

                    //Historique paiement scolarite

                    HistoriquePaiementScolariteModel::create([
                        'annee_scolaire_id' => AnneeScolaire()->id,
                        'caisse_id' => $caisse->id,
                        'inscription_id' => $inscriptionModel->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'matricule' => $validated['matricule'],
                        'frais_payer' => $validated['montant'],
                        'remise' => $validated['remise'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    //Historique Operation
                    HistoriqueOperationModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'sens' => "entree",
                        'montant' =>$validated['montant'],
                        'mode_paiement' => $validated['mode_paiement'],
                        'date_comptable' => $caisse->date_comptable,
                    ]);
                    // Mise à jour de la caisse

                    $caisse = CaisseModel::find($caisse->id);
                    $solde_caisse = $caisse->solde + $validated['montant'];

                    $caisse->update([
                        'solde' => $solde_caisse,
                    ]);
                    //Mouvement caisse

                    MouvementCaisseModel::create([
                        'caisse_id' => $caisse->id,
                        'user_id' => Auth::user()->id,
                        'description' => $libelle,
                        'entree' => $validated['montant'],
                        'sortie' => 0,
                        'solde' => $solde_caisse,
                        'date_comptable' => $caisse->date_comptable,
                    ]);

                    $paie_frais_scolaire = HistoriquePaiementScolariteModel::where('user_id', Auth::user()->id)->where('etat', 1)->latest('id')->first();
                    return redirect('/reglement/frais/scolaire/recu/' . encrypt($paie_frais_scolaire->id));
                }
            }
        } else {
            return redirect('/reglement/frais/scolaire')->with('success', "Pas d'annee scolaire actif");
        }
    }
}
