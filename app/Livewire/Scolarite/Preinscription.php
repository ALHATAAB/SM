<?php

namespace App\Livewire\Scolarite;

use App\Models\Config\ConfigMatriculeModel;
use App\Models\Scolarite\PreInscriptionModel;
use Illuminate\Http\Request;
use App\Services\PhotoService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Preinscription extends Component
{
    use WithFileUploads;

    public $photo;

    public $newNumber,$matricule,$nom,$prenom, $sexe, $groupe, $date_naissance,$lieu_naissance,$telephone,$adresse;
    public $prenom_parent,$telephone_parent,$adresse_parent;
    public $ConfigMatriculeApprenant;

    public function mount()
    {
       if( ConfigMatriculeModel::where('type','Apprenant')->count() >0 ){
        $this->ConfigMatriculeApprenant=ConfigMatriculeModel::where('type','Apprenant')->first();
        $latest = PreInscriptionModel::latest('id')->first();
        $newNumber = $latest ? intval($latest->id) + 1 : 1;
        $matricule = str_pad($newNumber,$this->ConfigMatriculeApprenant->nbr_caractere, '0', STR_PAD_LEFT);

        $this->matricule=$this->ConfigMatriculeApprenant->initiale.''.$matricule;
        $this->newNumber= $newNumber;
       }else{
        $latest = PreInscriptionModel::latest('id')->first();
        $newNumber = $latest ? intval($latest->id) + 1 : 1;
        $matricule = str_pad($newNumber,3, '0', STR_PAD_LEFT);

        $this->matricule=$matricule;
        $this->newNumber= $newNumber;
       }
       
    }

    public function render()
    {
        return view('livewire.scolarite.preinscription');
    }

    public function addPreInscription(Request  $request)
    {
        $validated = $this->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'sexe' => 'required|string',
            'groupe' => 'required|string',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string',
            'telephone' => ['required','string','regex:/^(?:\+227\d{8}|\d{8})$/'],
            // 'telephone' => 'required|string|unique:pre_inscription_models',
            'adresse' => 'required',

            'prenom_parent' => 'required',
            'telephone_parent' => ['required','string','regex:/^(?:\+227\d{8}|\d{8})$/'],
            'adresse_parent' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

       
        $eleve = PreInscriptionModel::create([
            'matricule'=>$this->matricule,
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'groupe'=>$validated['groupe'],
            'date_naissance'=>$validated['date_naissance'],
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'prenom_parent'=>$validated['prenom_parent'],
            'telephone_parent'=>$validated['telephone_parent'],
            'adresse_parent'=>$validated['adresse_parent'],
            ]);

            if ($this->photo) {
                PhotoService::enregistrer('apprenant', $eleve->id, $this->photo);
            }

            return redirect('recu/preinscription/'.encrypt($this->newNumber));

    }

}
