<?php

namespace App\Livewire\Scolarite;

use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\EmargementEnseignantModel;
use App\Models\Scolarite\EnseignantModel;
use Livewire\Component;

class DetailEnseignant extends Component
{
    public $enseignant;
    public $jours =['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    public $salaireCount,$type_salaire,$montant,$enseignant_id,$matricule,$nom, $prenom, $telephone,$adresse ,$date_naissance,$lieu_naissance,$sexe,$specialite,$date_embauche,$date_depart;


    public function mount($id)
    {
        $id=decrypt($id);

        $this->enseignant=EnseignantModel::find($id);

        $this->nom=$this->enseignant->nom;
        $this->prenom=$this->enseignant->prenom;
        $this->telephone=$this->enseignant->telephone;
        $this->adresse=$this->enseignant->adresse;
        $this->date_naissance=$this->enseignant->date_naissance;
        $this->lieu_naissance=$this->enseignant->lieu_naissance;
        $this->sexe=$this->enseignant->sexe;
        $this->specialite=$this->enseignant->specialite;
        $this->date_embauche=$this->enseignant->date_embauche;
        $this->date_depart=$this->enseignant->date_depart;
        $this->type_salaire=$this->enseignant->type_contrat;
        if($this->enseignant->type_contrat==='fixe'){
            $this->montant=$this->enseignant->salaire_base;
        }else
        {
             $this->montant=$this->enseignant->salaire_horaire;
        }
       

   
    }

    public function render()
    {
            $seances = SeanceModel::with(['matiere', 'enseignant', 'classe', 'salle'])
                ->where('enseignant_id',  $this->enseignant->id)
                ->orderBy('jour')
                ->orderBy('heure_debut')
                ->get()
                ->groupBy('jour');
                
           $emargements = EmargementEnseignantModel::orderBy('date',"DESC") ->where('enseignant_id',  $this->enseignant->id) ->whereBetween('date', [AnneeScolaire()->date_debut, AnneeScolaire()->date_fin])->paginate(5);

        return view('livewire.scolarite.detail-enseignant',[
            'seances' => $seances,
            'emargements' => $emargements,
        ]);
    }

    public function editerEnseignant()
    {
         $enseignant=EnseignantModel::find($this->enseignant->id);
        $validated = $this->validate(
            [
                'nom'=>'required',
                'prenom'=>'required',
                'sexe'=>'required',
                'date_naissance'=>'required',
                'date_embauche'=>'required|date',
                // vide tant que l'enseignant est en poste
                'date_depart'=>'nullable|date|after_or_equal:date_embauche',
                'lieu_naissance'=>'required',
                'telephone'=>'required',
                'type_salaire'=>'required',
                'montant'=>'required',
                'adresse'=>'required',
            ]
        );

         if($validated['type_salaire']!="horaire")
        {
            $enseignant->update([
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'date_naissance'=>$validated['date_naissance'],
            'date_embauche'=>$validated['date_embauche'],
            'date_depart'=>$validated['date_depart'] ?: null,
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'type_contrat'=>'fixe',
            'salaire_base'=>$validated['montant'],
            'salaire_horaire'=>0,
        ]);
        }else
        {
            $enseignant->update([
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'date_naissance'=>$validated['date_naissance'],
            'date_embauche'=>$validated['date_embauche'],
            'date_depart'=>$validated['date_depart'] ?: null,
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'type_contrat'=>'horaire',
            'salaire_base'=>0,
            'salaire_horaire'=>$validated['montant'],
        ]);
        }
          return redirect('detail/enseignant/'.encrypt($enseignant->id))->with('info','Modification effectuée avec succès');
       
    }
}
