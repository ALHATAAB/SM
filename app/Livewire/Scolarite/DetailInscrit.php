<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\HistoriqueAbonnementModel;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\PreInscriptionModel;
use App\Livewire\Concerns\VerifiePermission;
use Livewire\Attributes\Locked;
use Livewire\Component;

class DetailInscrit extends Component
{
    use VerifiePermission;

    // #[Locked] : sinon le client pourrait changer inscrit.pre_inscription_id
    // et faire modifier la fiche d'un AUTRE apprenant.
    #[Locked]
    public $inscrit;
    public $inscriptions = [];
    public $matieres = [];
    public $apprenants = [];
    public $niveau, $filiere, $matiere, $frais_inscription, $coefficient, $coefficient_id, $niveau_id, $filiere_id;
    public $matricule, $nom, $prenom, $sexe, $groupe, $date_naissance, $lieu_naissance, $telephone, $adresse;
    public $prenom_parent, $telephone_parent, $adresse_parent;
    public $coefficientMatiere = [];
    public $coefficientCount;
    public $mode_paiement;
    public $caisse = [];
    public $countCaisse;
    public $historiquePaiements = [];
    public $historiqueAbonnements=[];
    public $somme_frais_inscription, $somme_frais_scolarite;


    public function mount($id)
    {
        $this->exigerPermissions('Scolarite', 'Consulter');

        $id = $this->idDepuisUrl($id);
        $this->inscrit = InscriptionModel::where('etat', 1)->findOrFail($id);
        $preinscription = PreInscriptionModel::findOrFail($this->inscrit->pre_inscription_id);

        $anneeId = AnneeScolaire()?->id;
        abort_if(!$anneeId, 404, "Aucune année scolaire active.");

        $this->nom = $preinscription->nom;
        $this->prenom = $preinscription->prenom;
        $this->sexe = $preinscription->sexe;
        $this->groupe = $preinscription->groupe;
        $this->telephone = $preinscription->telephone;
        $this->date_naissance = $preinscription->date_naissance;
        $this->lieu_naissance = $preinscription->lieu_naissance;
        $this->adresse = $preinscription->adresse;

        $this->prenom_parent = $preinscription->prenom_parent;
        $this->telephone_parent = $preinscription->telephone_parent;
        $this->adresse_parent = $preinscription->adresse_parent;

        $this->somme_frais_inscription = InscriptionModel::where("pre_inscription_id", $this->inscrit->pre_inscription_id)
            ->where('annee_scolaire_id', $anneeId)->where('etat', 1)
            ->sum('frais_inscription');
        $this->somme_frais_scolarite = InscriptionModel::where("pre_inscription_id", $this->inscrit->pre_inscription_id)->where('etat', 1)
            ->where('annee_scolaire_id', $anneeId)
            ->sum('frais_scolarite');



        $this->inscriptions = InscriptionModel::where("pre_inscription_id", $this->inscrit->pre_inscription_id)->where('etat', 1)
            ->get();
        $this->historiquePaiements = HistoriquePaiementScolariteModel::where("matricule", $this->inscrit->matricule)
            ->where('etat','!=',0) 
            ->where('annee_scolaire_id', $anneeId)
            ->get();
         $this->historiqueAbonnements = HistoriqueAbonnementModel::where("matricule", $this->inscrit->matricule)
            ->where('annee_scolaire_id', $anneeId)
            ->where('etat','!=',0) 
            ->get();
    }

    public function render()
    {
        return view('livewire.scolarite.detail-inscrit');
    }


    public function editPreInscription()
    {
        $this->exigerPermissions('Scolarite', 'Modifier');

        $preinscription = PreInscriptionModel::findOrFail($this->inscrit->pre_inscription_id);

        $validated = $this->validate([
             'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|string|max:255',
            'groupe' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'telephone' => ['required','string','regex:/^(?:\+227\d{8}|\d{8})$/'],
            // 'telephone' => 'required|string|unique:pre_inscription_models',
            'adresse' => 'required|string|max:255',

            'prenom_parent' => 'required|string|max:255',
            'telephone_parent' => ['required','string','regex:/^(?:\+227\d{8}|\d{8})$/'],
            'adresse_parent' => 'required|string|max:255',
            
        ]);


        $preinscription->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'sexe' => $validated['sexe'],
            'groupe' => $validated['groupe'],
            'date_naissance' => $validated['date_naissance'],
            'lieu_naissance' => $validated['lieu_naissance'],
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'prenom_parent' => $validated['prenom_parent'],
            'telephone_parent' => $validated['telephone_parent'],
            'adresse_parent' => $validated['adresse_parent'],
        ]);


        return redirect('/detail/inscrit/' . encrypt($this->inscrit->id))->with('info', 'Modification effectuée avec succès');
    }
}
