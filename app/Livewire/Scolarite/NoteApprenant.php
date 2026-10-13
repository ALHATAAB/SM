<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\CoefficientMatiereModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\NoteApprenantModel;
use App\Models\Scolarite\TrimestreModel;
use Livewire\Component;

class NoteApprenant extends Component
{
    public $classes;
    public $classe_id, $matiere_id;
    public $trimestre;
    public $type = 'classe';

    public $matieres = [];
    public $trimestres = [];
    public $apprenants = [];
    public $notes = [];

    public function mount()
    {
        $this->trimestres = TrimestreModel::where('annee_scolaire_id', AnneeScolaire()->id)->get();
        $this->classes = ClasseEtudeModel::all();
    }

    public function updatedClasseId($classeId)
    {
        $this->matieres = CoefficientMatiereModel::where('classe_etude_id', $classeId)->get();
        $this->loadApprenants();
    }

    public function updatedMatiereId()
    {
        $this->loadApprenants();
    }

    public function updatedTrimestre()
    {
        $this->loadApprenants();
    }

    public function updatedType()
    {
        $this->loadApprenants();
    }

    public function loadApprenants()
    {
        if ($this->classe_id && $this->matiere_id && $this->trimestre) {
            $this->apprenants = InscriptionModel::where('classe_etude_id', $this->classe_id)->where('annee_scolaire_id', AnneeScolaire()->id)->get();

            foreach ($this->apprenants as $apprenant) {
                $note = NoteApprenantModel::where([
                    'apprenant_id' => $apprenant->id,
                    'classe_etude_id' => $this->classe_id,
                    'matiere_etude_id' => $this->matiere_id,
                    'trimestre_id' => $this->trimestre,
                    'type' => $this->type,
                ])->first();

                $this->notes[$apprenant->id] = $note->note ?? '';
            }
        } else {
            $this->apprenants = [];
            $this->notes = [];
        }
    }

    public function saveNotes()
    {
        foreach ($this->notes as $apprenant_id => $val) {
            NoteApprenantModel::updateOrCreate([
                'apprenant_id' => $apprenant_id,
                'classe_etude_id' => $this->classe_id,
                'matiere_etude_id' => $this->matiere_id,
                'trimestre_id' => $this->trimestre,
                'type' => $this->type,
            ], [
                'note' => $val,
            ]);
        }

        session()->flash('message', 'Notes enregistrées avec succès !');
    }

    public function render()
    {
        return view('livewire.scolarite.note-apprenant');
    }
}
