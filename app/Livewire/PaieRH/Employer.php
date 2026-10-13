<?php

namespace App\Livewire\PaieRH;

use App\Models\Config\ConfigMatriculeModel;
use App\Models\PaieRH\EmployerModel;
use App\Livewire\Concerns\VerifiePermission;
use App\Services\CarteService;
use App\Services\PhotoService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Employer extends Component
{
    use WithPagination, WithFileUploads, VerifiePermission;
    /** Générer la carte professionnelle dès l'enregistrement (case cochée par défaut). */
    public $generer_carte = true;
    public $photo;
    protected $paginationTheme = 'bootstrap';
    public $rechercheNom;
    public $recherchePrenom;
    public $rechercheTelephone,$ConfigMatriculeEmployer;
    
    public $employer_id,$matricule,$nom, $prenom, $telephone,$adresse,$email,$date_naissance,$lieu_naissance,$sexe,$poste,$charge_familliale,$salaire,$date_embauche;

    public function mount()
    {
        if( ConfigMatriculeModel::where('type','Employer')->count() >0 ){
            $this->ConfigMatriculeEmployer=ConfigMatriculeModel::where('type','Employer')->first();
            $latest = EmployerModel::latest('id')->first();
            $newNumber = $latest ? intval($latest->id) + 1 : 1;
            $matricule = str_pad($newNumber,$this->ConfigMatriculeEmployer->nbr_caractere, '0', STR_PAD_LEFT);
    
            $this->matricule=$this->ConfigMatriculeEmployer->initiale.''.$matricule;
           }else{
            $latest = EmployerModel::latest('id')->first();
            $newNumber = $latest ? intval($latest->id) + 1 : 1;
            $matricule = str_pad($newNumber,3, '0', STR_PAD_LEFT);
    
            $this->matricule=$matricule;
           }
    }
    
    public function render()
    {
          //condition de recherche utilisateur
          if($this->rechercheNom || $this->recherchePrenom || $this->rechercheTelephone){
            $employers = EmployerModel::where("nom",'like','%'.$this->rechercheNom.'%')->where("prenom",'like','%'.$this->recherchePrenom.'%')
            ->where("telephone",'like','%'.$this->rechercheTelephone.'%')
            ->paginate(10);
        }else{
            $employers = EmployerModel::paginate(10);
        }

        return view('livewire.paie-r-h.employer',[
            'employers'=>$employers,
            'cartes'=>app(CarteService::class)->cartesProValides('employer', $employers->getCollection()->pluck('id')),
        ]);
    }

    public function addEmployer()
    {
        $validated = $this->validate(
            [
                'matricule'=>'required',
                'nom'=>'required',
                'prenom'=>'required',
                'sexe'=>'required',
                'date_naissance'=>'required',
                'lieu_naissance'=>'required',
                'telephone'=>'required',
                'adresse'=>'required',
                // 'email'=>'required|email|unique:employer_models',
                'poste'=>'required',
                'charge_familliale'=>'nullable|integer|min:0',
                // 'salaire'=>'required',
                'date_embauche'=>'required',
                'photo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]
        );

        $employe = EmployerModel::create([
            'matricule'=>$this->matricule,
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'date_naissance'=>$validated['date_naissance'],
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            // 'email'=>$validated['email'],
            'poste'=>$validated['poste'],
            'charge_familliale'=>(int) ($validated['charge_familliale'] ?? 0),
            // 'salaire'=>$validated['salaire'],
            'date_embauche'=>$validated['date_embauche'],
        ]);

        if ($this->photo) {
            PhotoService::enregistrer('employer', $employe->id, $this->photo);
        }

        $message = 'Opération effectuée avec succès';
        $type = 'success';
        if ($this->generer_carte && !empty(Permission('Ajouter'))) {
            try {
                $carte = app(CarteService::class)->emettre(CarteService::PROFESSIONNELLE, 'employer', $employe->id);
                $message .= ' — carte professionnelle ' . $carte->numero . ' générée.';
            } catch (\RuntimeException $e) {
                $message .= ' — carte professionnelle non générée : ' . $e->getMessage();
                $type = 'warning';
            }
        }

        return redirect('/employer')->with($type, $message);
    }

    /** Génère (ou régénère si besoin) la carte professionnelle depuis la liste. */
    public function genererCarte(int $id): void
    {
        $this->exigerPermissions('Employer', 'Ajouter');

        try {
            $carte = app(CarteService::class)->emettre(CarteService::PROFESSIONNELLE, 'employer', $id);
            session()->flash('success', 'Carte professionnelle ' . $carte->numero . ' générée.');
        } catch (\RuntimeException $e) {
            session()->flash('danger', $e->getMessage());
        }
    }
}
