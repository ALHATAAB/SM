<?php

namespace App\Livewire\Scolarite;

use App\Models\Scolarite\AbsenceApprenantModel;
use Livewire\Component;
use Livewire\WithPagination;

class ListeAbsenceApprenant extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $dateDebut;
    public $dateFin;

    public function updatedDateDebut()
    {
        $this->resetPage();
    }

    public function updatedDateFin()
    {
        $this->resetPage();
    }

    public function render()
    {
        $annee = AnneeScolaire();

        // Absences des élèves inscrits dans l'année scolaire active (et non plus d'après la
        // seule date de l'absence, qui masquait les absences saisies hors des dates de l'année).
        $absences = AbsenceApprenantModel::with(['inscription.preInscription', 'classe', 'matiere', 'enseignant', 'salle'])
            ->when($annee, fn ($q) => $q->whereHas('inscription', fn ($i) => $i->where('annee_scolaire_id', $annee->id)))
            ->when($this->dateDebut, fn ($q) => $q->whereDate('date', '>=', $this->dateDebut))
            ->when($this->dateFin, fn ($q) => $q->whereDate('date', '<=', $this->dateFin))
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('livewire.scolarite.liste-absence-apprenant', [
            'absences' => $absences,
        ]);
    }
}
