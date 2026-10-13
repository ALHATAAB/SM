<?php

namespace App\Livewire\Scolarite;

use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Config\ModePaiementModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\CoefficientMatiereModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\FraisScolaireModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Concerns\VerifiePermission;
use Livewire\Attributes\Locked;
use Livewire\Component;

class InscriptionEdite extends Component
{
    use VerifiePermission;

    public $niveaux = [];
    public $filieres = [];
    public $classes = [];
    public $matieres = [];
    public $apprenants = [];
    public $classe, $niveau, $filiere, $matiere, $matricule, $frais_inscription, $frais_scolarite, $coefficient, $coefficient_id, $niveau_id, $filiere_id;
    public $nom, $prenom, $sexe, $groupe, $telephone;
    public $coefficientMatiere = [];
    public $coefficientCount;
    public $inscription;

    // Non modifiable depuis le navigateur
    #[Locked]
    public $inscription_id;
    public $mode_paie;
    public $mode_paiements = [];
    public $caisses = [];
    public $caisse;
    public $banques = [];
    public $banque;
    public $countCaisse;

    public function mount($id)
    {
        $this->exigerPermissions('Scolarite', 'Modifier');

        $id = $this->idDepuisUrl($id);

        $inscription = InscriptionModel::with('preInscription')->where('etat', 1)->findOrFail($id);
        $this->inscription_id = $inscription->id;
        if ($inscription) {
            $this->matricule = $inscription->matricule;
            $this->classe = $inscription->classe_etude_id;
            $this->niveau = $inscription->niveau_etude_id;

            if ($inscription->preInscription) {
                $this->nom = $inscription->preInscription->nom;
                $this->prenom = $inscription->preInscription->prenom;
                $this->sexe = $inscription->preInscription->sexe;
                $this->groupe = $inscription->preInscription->groupe;
                $this->telephone = $inscription->preInscription->telephone;
            }

            // Chargement des filières pour le niveau déjà sélectionné
            $this->filieres = FraisScolaireModel::where('niveau_etude_id', $this->niveau)->get();

            // Récupération de la filière associée à l’inscription
            $frais = FraisScolaireModel::where('niveau_etude_id', $this->niveau)
                ->whereNotNull('filiere_etude_id')
                ->first();

            $this->filiere = $frais?->filiere_etude_id ?? null;
            $this->frais_inscription = $frais?->frais_inscription ?? null;
            $this->frais_scolarite = $frais?->frais_scolaire ?? null;
        }

        $this->countCaisse = CaisseModel::where('user_id', Auth::id())->count();
        $this->caisse = CaisseModel::where('user_id', Auth::id())->first();

        $this->mode_paiements = ModePaiementModel::all();
        $this->niveaux = NiveauEtudeModel::all();
        $this->classes = ClasseEtudeModel::all();
        $this->banques = BanqueModel::all();
    }



    public function updatedNiveau($niveauId)
    {

        $this->filieres = FraisScolaireModel::where('niveau_etude_id', $niveauId)->get();
        $this->filiere = "";

        $this->frais_inscription = "";
        $this->frais_scolarite = "";
        $this->niveau_id = $niveauId;
    }

    public function updatedFiliere($filiereId)
    {
        $this->filiere_id = $filiereId;
        // $this->coefficientCount = FraisScolaireModel::find('filiere_etude_id',$this->niveau_id)
        // ->where('filiere_etude_id',$this->filiere_id)
        // ->count();
        $frais = FraisScolaireModel::where('niveau_etude_id', $this->niveau_id)
            ->where('filiere_etude_id', $this->filiere_id)
            ->first();

        $this->frais_inscription = $frais?->frais_inscription ?? null;
        $this->frais_scolarite = $frais?->frais_scolaire ?? null;
        // dd( $this->coefficientMatiere );
    }


    public function render()
    {
        return view('livewire.scolarite.inscription-edite');
    }

    public function EditInscription()
    {
        $this->exigerPermissions('Scolarite', 'Modifier');

        $inscription = InscriptionModel::where('etat', 1)->findOrFail($this->inscription_id);

        $validated = $this->validate([
            'niveau' => ['required', 'integer', 'exists:niveau_etude_models,id'],
            'filiere' => ['required', 'integer', 'exists:filiere_etude_models,id'],
        ]);

        // Les frais NE viennent PLUS du navigateur (champs modifiables par l'utilisateur) :
        // on les relit dans la grille tarifaire pour ce niveau + cette filière.
        $frais = FraisScolaireModel::where('niveau_etude_id', $validated['niveau'])
            ->where('filiere_etude_id', $validated['filiere'])
            ->first();

        if (!$frais) {
            return redirect('/editer/inscrit/' . encrypt($this->inscription_id))
                ->with('danger', 'Aucun frais défini pour ce niveau et cette filière');
        }

        $validated['frais_inscription'] = $frais->frais_inscription;
        $validated['frais_scolarite'] = $frais->frais_scolaire;

        if ($inscription->frais_inscription == $validated['frais_inscription']) {
            if ($validated['frais_scolarite'] < $inscription->payer) {
                return redirect('/editer/inscrit/' . encrypt($this->inscription_id))->with('info', 'Vous avez déjà soldé');
            } else {
                    if ($inscription->payer != null && $inscription->rester != null) {
                $rester = $validated['frais_scolarite'] - $inscription->payer;
                $payer = $inscription->payer;
            } elseif ($inscription->payer != null && $inscription->rester == null) {
                $payer = $inscription->payer;
                $rester = $rester = $validated['frais_scolarite'] - $inscription->payer;
            } else {
                $payer = $inscription->payer;
                $rester = null;
            }
            //inscription
            $inscription->update(
                [
                    'niveau_etude_id' => $validated['niveau'],
                    'filiere_etude_id' => $validated['filiere'],
                    'frais_inscription' => $validated['frais_inscription'],
                    'frais_scolarite' => $validated['frais_scolarite'],
                    'payer' => $payer,
                    'rester' => $rester,
                ]
            );
            return redirect('/editer/inscrit/' . encrypt($this->inscription_id))->with('success', 'Opération effectuée avec succès');
        
            }
        }elseif ($inscription->frais_inscription > $validated['frais_inscription']) {
            if ($validated['frais_scolarite'] < $inscription->payer) {
                return redirect('/editer/inscrit/' . encrypt($this->inscription_id))->with('info', 'Vous avez déjà soldé');
            } else {
                    if ($inscription->payer != null && $inscription->rester != null) {
                $inscrit=$inscription->frais_inscription-$validated['frais_inscription'];
                $rester = ($validated['frais_scolarite'] - $inscription->payer)-$inscrit;
                $payer = $inscription->payer+$inscrit;
            } elseif ($inscription->payer != null && $inscription->rester == null) {
                 $inscrit=$inscription->frais_inscription-$validated['frais_inscription'];
                $payer = $inscription->payer+$inscrit;
                $rester = $rester = ($validated['frais_scolarite'] - $inscription->payer)-$inscrit;
            } else {
                $payer = $inscription->payer;
                $rester = null;
            }

            //inscription
            $inscription->update(
                [
                    'niveau_etude_id' => $validated['niveau'],
                    'filiere_etude_id' => $validated['filiere'],
                    'frais_inscription' => $validated['frais_inscription'],
                    'frais_scolarite' => $validated['frais_scolarite'],
                    'payer' => $payer,
                    'rester' => $rester,
                ]
            );
            return redirect('/editer/inscrit/' . encrypt($this->inscription_id))->with('success', 'Opération effectuée avec succès');
        
            }
        }
        else {
            return redirect('/editer/inscrit/' . encrypt($this->inscription_id))->with('warning', "Vous ne pouvez pas modifier; le frais d'inscription est different du frais initiale");
        }
    }
}
