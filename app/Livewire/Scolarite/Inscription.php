<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\HistoriqueOperationBanqueModel;
use App\Models\Comptabilite\HistoriqueOperationModel;
use App\Models\Comptabilite\MouvementBanqueModel;
use App\Models\Comptabilite\MouvementCaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\FraisScolaireModel;
use App\Models\Scolarite\HistoriqueInscriptionModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use App\Models\Scolarite\PreInscriptionModel;
use App\Services\LicenceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Inscription extends Component
{
    public $niveaux = [], $filieres = [], $classes = [], $matieres = [], $apprenants = [];
    public $classe, $niveau, $filiere, $matiere, $matricule, $frais_inscription, $frais_scolaire;
    public $coefficient, $coefficient_id, $niveau_id, $filiere_id;
    public $nom, $prenom, $sexe, $groupe, $telephone;
    public $coefficientMatiere = [], $coefficientCount;
    public $mode_paiement, $coordonnee_paiement, $mode_paie;
    public $mode_paiements = [], $caisses = [], $banques = [];
    public $caisse, $banque, $countCaisse;

    public function mount()
    {
        $userId = Auth::id();
        $this->countCaisse = CaisseModel::where('user_id', $userId)->count();
        $this->caisse = CaisseModel::where('user_id', $userId)->first();

        $this->mode_paiements = ModePaiementModel::all();
        $this->niveaux = NiveauEtudeModel::all();
        $this->matieres = MatiereEtudeModel::all();
        $this->classes = ClasseEtudeModel::all();
        $this->banques = BanqueModel::all();

        // Enchaînement depuis la préinscription : /inscription?matricule=XXX ouvre le formulaire déjà rempli
        $matricule = request()->query('matricule');
        if (is_string($matricule) && $matricule !== '') {
            $this->matricule = $matricule;
            $this->updatedMatricule($matricule);
        }
    }

    public function updatedMatricule($matriculeId)
    {
        $apprenant = PreInscriptionModel::where('matricule', $matriculeId)->first();
        if ($apprenant) {
            $this->nom = $apprenant->nom;
            $this->prenom = $apprenant->prenom;
            $this->sexe = $apprenant->sexe;
            $this->groupe = $apprenant->groupe;
            $this->telephone = $apprenant->telephone;
        } else {
            $this->reset(['nom', 'prenom', 'sexe', 'groupe', 'telephone']);
        }
    }

    public function updatedNiveau($niveauId)
    {
        $this->filieres = FraisScolaireModel::where('niveau_etude_id', $niveauId)->get();
        $this->filiere = "";
        $this->niveau_id = $niveauId;
    }

    public function updatedFiliere($filiereId)
    {
        $this->filiere_id = $filiereId;
        $frais = FraisScolaireModel::where('niveau_etude_id', $this->niveau_id)
            ->where('filiere_etude_id', $this->filiere_id)->first();

        if ($frais) {
            $this->frais_inscription = $frais->frais_inscription;
            $this->frais_scolaire = $frais->frais_scolaire;
        }
    }

    public function updatedModePaiement($modePaiementId)
    {
        $mode = ModePaiementModel::where('mode_paiement', $modePaiementId)->first();
        $this->mode_paie = $mode ? $mode->mode_paiement : null;
    }

    public function render()
    {
        return view('livewire.scolarite.inscription');
    }

    public function addInscription()
    {
        $this->caisse = CaisseModel::where('user_id', Auth::id())->first();

        $isBanque = in_array($this->mode_paie, ['DEPOT', 'TRANSFERT', 'CHEQUE', 'VIREMENT']);

        $rules = [
            'matricule' => 'required',
            'niveau' => 'required',
            'filiere' => 'required',
            'classe' => 'required',
            'frais_inscription' => 'required',
            'mode_paiement' => 'required',
        ];

        if ($isBanque) {
            $rules['coordonnee_paiement'] = 'required';
            $rules['banque'] = 'required';
        }

        $validated = $this->validate($rules);

        // L'année scolaire active doit exister avant toute lecture de AnneeScolaire()->id
        if (!AnneeScolaire()) {
            return redirect('/inscription')->with('danger', "Pas d'annee scolaire actif");
        }

        $preInscription = PreInscriptionModel::where('matricule', $validated['matricule'])->first();

        $exists = InscriptionModel::where("pre_inscription_id", $preInscription->id)
            ->where('niveau_etude_id', $validated['niveau'])
            ->where('filiere_etude_id', $validated['filiere'])
            ->where('annee_scolaire_id', AnneeScolaire()->id)
            ->where('etat', 1)
            ->exists();

        if ($exists) {
            return redirect('/inscription')->with('danger', 'Apprenant déjà inscrit');
        }

        $licenceService = app(LicenceService::class);
        if (!$licenceService->inscritDisponible()) {
            return redirect('/inscription')->with('danger',
                "Nombre maximum d'inscrits atteint pour votre licence (" .
                $licenceService->nombreInscritsActuel() . " / " . $licenceService->limiteInscrits() .
                "). Contactez votre fournisseur pour augmenter votre quota."
            );
        }

        $frais = FraisScolaireModel::where('niveau_etude_id', $validated['niveau'])
            ->where('filiere_etude_id', $validated['filiere'])->first();

        $libelle = "Inscription de l'ann\u00e9e " . AnneeScolaire()->annee . " : " . $this->nom . " " . $this->prenom . " Matricule : " . $validated['matricule'];

        InscriptionModel::create([
            'matricule' => $validated['matricule'],
            'pre_inscription_id' => $preInscription->id,
            'niveau_etude_id' => $validated['niveau'],
            'filiere_etude_id' => $validated['filiere'],
            'classe_etude_id' => $validated['classe'],
            'frais_inscription' => $frais->frais_inscription,
            'frais_scolarite' => $frais->frais_scolaire,
            'annee_scolaire_id' => AnneeScolaire()->id,
        ]);

        if ($isBanque) {
            $banque = BanqueModel::find($validated['banque']);
            $banque->update(['solde' => $banque->solde + $validated['frais_inscription']]);

            HistoriqueInscriptionModel::create([
            'annee_scolaire_id' => AnneeScolaire()->id,
            'banque_id' => $banque->id,
            'user_id' => Auth::id(),
            'description' => $libelle,
            'matricule' => $validated['matricule'],
            'pre_inscription_id' => $preInscription->id,
            'niveau_etude_id' => $validated['niveau'],
            'filiere_etude_id' => $validated['filiere'],
            'classe_etude_id' => $validated['classe'],
            'frais_inscription' => $validated['frais_inscription'],
            'mode_paiement' => $validated['mode_paiement'],
            'coordonnee_paiement' => $validated['coordonnee_paiement'] ?? null,
            'date_comptable' => $this->caisse->date_comptable,

            ]);

            MouvementBanqueModel::create([
                'banque_id' => $banque->id,
                'user_id' => Auth::id(),
                'description' => $libelle,
                'entree' => $validated['frais_inscription'],
                'sortie' => 0,
                'solde' => $banque->solde,
                'date_comptable' => $this->caisse->date_comptable,
            ]);
        } else {

            HistoriqueInscriptionModel::create([
            'annee_scolaire_id' => AnneeScolaire()->id,
            'caisse_id' => $this->caisse->id,
            'user_id' => Auth::id(),
            'description' => $libelle,
            'matricule' => $validated['matricule'],
            'pre_inscription_id' => $preInscription->id,
            'niveau_etude_id' => $validated['niveau'],
            'filiere_etude_id' => $validated['filiere'],
            'classe_etude_id' => $validated['classe'],
            'frais_inscription' => $validated['frais_inscription'],
            'mode_paiement' => $validated['mode_paiement'],
            'coordonnee_paiement' => $validated['coordonnee_paiement'] ?? null,
            'date_comptable' => $this->caisse->date_comptable,
        ]);

            HistoriqueOperationModel::create([
                'caisse_id' => $this->caisse->id,
                'user_id' => Auth::id(),
                'description' => $libelle,
                'sens' => "entree",
                'montant' => $validated['frais_inscription'],
                'mode_paiement' => $validated['mode_paiement'],
                'date_comptable' => $this->caisse->date_comptable,
            ]);

            $this->caisse->update(['solde' => $this->caisse->solde + $validated['frais_inscription']]);

            MouvementCaisseModel::create([
                'caisse_id' => $this->caisse->id,
                'user_id' => Auth::id(),
                'description' => $libelle,
                'entree' => $validated['frais_inscription'],
                'sortie' => 0,
                'solde' => $this->caisse->solde,
                'date_comptable' => $this->caisse->date_comptable,
            ]);
        }

        $post_inscription = HistoriqueInscriptionModel::where('user_id', Auth::id())->latest('id')->first();
        return redirect('/inscription/recu/' . encrypt($post_inscription->id));
    }
}