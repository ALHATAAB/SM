<?php

namespace App\Livewire\Scolarite;

use App\Models\Config\ConfigMatriculeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Livewire\Concerns\VerifiePermission;
use App\Services\CarteService;
use App\Services\PhotoService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Enseignant extends Component
{
    use WithPagination, WithFileUploads, VerifiePermission;
    /** Générer la carte professionnelle dès l'enregistrement (case cochée par défaut). */
    public $generer_carte = true;
    public $photo;
    protected $paginationTheme = 'bootstrap';
    public $rechercheNom;
    public $recherchePrenom;
    public $rechercheTelephone,$ConfigMatriculeEnseignant;
    // public $enseignants;
    public $salaireCount,$type_salaire,$montant,$enseignant_id,$matricule,$nom, $prenom, $telephone,$adresse ,$date_naissance,$lieu_naissance,$sexe,$specialite,$date_embauche;

    public function mount()
    {
        if( ConfigMatriculeModel::where('type','Enseignant')->count() >0 ){
            $this->ConfigMatriculeEnseignant=ConfigMatriculeModel::where('type','Enseignant')->first();
            $latest = EnseignantModel::latest('id')->first();
            $newNumber = $latest ? intval($latest->id) + 1 : 1;
            $matricule = str_pad($newNumber,$this->ConfigMatriculeEnseignant->nbr_caractere, '0', STR_PAD_LEFT);
    
            $this->matricule=$this->ConfigMatriculeEnseignant->initiale.''.$matricule;
           }else{
            $latest = EnseignantModel::latest('id')->first();
            $newNumber = $latest ? intval($latest->id) + 1 : 1;
            $matricule = str_pad($newNumber,3, '0', STR_PAD_LEFT);
    
            $this->matricule=$matricule;
           }
    }
   

    public function render()
    {
          //condition de recherche utilisateur
          if($this->rechercheNom || $this->recherchePrenom || $this->rechercheTelephone){
            $enseignants = EnseignantModel::where("nom",'like','%'.$this->rechercheNom.'%')->where("prenom",'like','%'.$this->recherchePrenom.'%')
            ->where("telephone",'like','%'.$this->rechercheTelephone.'%')
            ->paginate(5);
        }else{
            $enseignants = EnseignantModel::paginate(5);
        }

        return view('livewire.scolarite.enseignant',[
            'enseignants'=>$enseignants,
            'cartes'=>app(CarteService::class)->cartesProValides('enseignant', $enseignants->getCollection()->pluck('id')),
        ]);
    }

    public function addEnseignant()
    {
        $validated = $this->validate(
            [
                'nom'=>'required',
                'prenom'=>'required',
                'sexe'=>'required',
                'date_naissance'=>'required',
                'date_embauche'=>'required|date',
                'lieu_naissance'=>'required',
                'telephone'=>'required',
                'type_salaire'=>'required',
                'montant'=>'required',
                'adresse'=>'required',
                'photo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]
        );
        if($validated['type_salaire']!="horaire")
        {
            $enseignant = EnseignantModel::create([
            'matricule'=>$this->matricule,
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'date_naissance'=>$validated['date_naissance'],
            'date_embauche'=>$validated['date_embauche'],
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'type_contrat'=>'fixe',
            'salaire_base'=>$validated['montant'],
        ]);
        }else
        {
            $enseignant = EnseignantModel::create([
            'matricule'=>$this->matricule,
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'date_naissance'=>$validated['date_naissance'],
            'date_embauche'=>$validated['date_embauche'],
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'type_contrat'=>'horaire',
            'salaire_horaire'=>$validated['montant'],
        ]);
        }
        if ($this->photo) {
            PhotoService::enregistrer('enseignant', $enseignant->id, $this->photo);
        }

        $message = 'Opération effectuée avec succès';
        $type = 'success';
        if ($this->generer_carte && !empty(Permission('Ajouter'))) {
            try {
                $carte = app(CarteService::class)->emettre(CarteService::PROFESSIONNELLE, 'enseignant', $enseignant->id);
                $message .= ' — carte professionnelle ' . $carte->numero . ' générée.';
            } catch (\RuntimeException $e) {
                $message .= ' — carte professionnelle non générée : ' . $e->getMessage();
                $type = 'warning';
            }
        }

        return redirect('/liste/enseignant')->with($type, $message);
    }

    /** Génère (ou régénère si besoin) la carte professionnelle depuis la liste. */
    public function genererCarte(int $id): void
    {
        $this->exigerPermissions('Scolarite', 'Ajouter');

        try {
            $carte = app(CarteService::class)->emettre(CarteService::PROFESSIONNELLE, 'enseignant', $id);
            session()->flash('success', 'Carte professionnelle ' . $carte->numero . ' générée.');
        } catch (\RuntimeException $e) {
            session()->flash('danger', $e->getMessage());
        }
    }
}
