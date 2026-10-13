<?php

namespace App\Livewire\Scolarite;

use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\AbsenceApprenantModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\PreInscriptionModel;
use Livewire\Component;

class AbsenceApprenant extends Component
{
    public $classes = [];
    public $classe = '';
    public $classeSelect = '';
    public $apprenants = [];
    public $enseignant_id;
    public $enseignant;
    public $seance_id;
    public $courses = [];
    public $coursesCount;

    public $absents = []; // tableau d’IDs cochés
    public $date, $motif, $justifiee = false;
    public $inscription_id;

    public function mount()
    {

        //liste des classe
        $this->classes = ClasseEtudeModel::all();
        $this->absents = [];
    }

    public function updatedClasse($classeId)
    {
        $this->apprenants = InscriptionModel::where('classe_etude_id', $classeId)
            ->where('annee_scolaire_id', AnneeScolaire()->id)
            ->get();
        $this->classeSelect = ClasseEtudeModel::find($classeId);
    }

    public function updatedEnseignant($enseignantId)
    {

        $this->coursesCount =  SeanceModel::where('enseignant_id', $enseignantId)
            ->count();
        $this->courses = SeanceModel::where('enseignant_id', $enseignantId)
            ->orderBy('jour')->orderBy('heure_debut')
            ->get();

        // dd( $this->coefficientMatiere );
    }

    public function render()
    {
        $teachers = EnseignantModel::all();
        return view('livewire.scolarite.absence-apprenant', [
            'teachers' => $teachers,
        ]);
    }


    public function enregistrer()
    {
        $this->validate([
            'classe' => 'required',
            'enseignant' => 'required',
            'seance_id' => 'required',
            'absents' => 'required|array|min:1',
            'date' => 'required|date',
        ]);

        $nbAjoutees = 0;
        $doublons = [];

        foreach ($this->absents as $inscription_id) {
            $seance = $seance = SeanceModel::find($this->seance_id);

            // Vérifie si une absence existe déjà pour cet apprenant à cette date
            $existe = AbsenceApprenantModel::where('inscription_id', $inscription_id)
                ->where('classe_etude_id', $this->classe)
                ->where('enseignant_id', $this->enseignant)
                ->where('matiere_etude_id', $seance->matiere_id)
                ->where('salle_etude_id', $seance->salle_id)
                ->where('heure_debut', $seance->heure_debut)
                ->where('heure_fin',  $seance->heure_fin)
                ->where('date', $this->date)
                ->exists();

            if (!$existe) {

                AbsenceApprenantModel::create([
                    'inscription_id' => $inscription_id,
                    'classe_etude_id' => $this->classe,

                    'enseignant_id' => $this->enseignant,
                    'matiere_etude_id' => $seance->matiere_id,
                    'salle_etude_id' => $seance->salle_id,

                    'heure_debut' => $seance->heure_debut,
                    'heure_fin' => $seance->heure_fin,

                    'date' => $this->date,
                    'motif' => $this->motif,
                    'justifiee' => $this->justifiee,
                ]);
                $nbAjoutees++;
            } else {
                // Garde en mémoire les doublons
                $apprenant = InscriptionModel::find($inscription_id);
                $doublons[] = $apprenant ? $apprenant->preInscription->nom . ' ' . $apprenant->preInscription->prenom : 'ID ' . $inscription_id;
            }
        }

        // Message récapitulatif
        if ($nbAjoutees > 0) {
            return  redirect('/absence/apprenant/')->with('info', "$nbAjoutees absences enregistrées avec succès.");
        }

        if (!empty($doublons)) {
            return  redirect('/absence/apprenant/')->with('warning', "Absences déjà existantes pour : " . implode(', ', $doublons));
        }

        $this->reset(['absents', 'date', 'motif', 'justifiee']);
        return  redirect('/absence/apprenant/');
        // $this->updatedClasse(); // recharge la liste des élèves
    }
}
