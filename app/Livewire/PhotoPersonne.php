<?php

namespace App\Livewire;

use App\Livewire\Concerns\VerifiePermission;
use App\Services\PhotoService;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Photo d'une personne (élève, enseignant, employé), à intégrer dans les pages de détail :
 *   @livewire('photo-personne', ['type' => 'enseignant', 'personneId' => $enseignant->id])
 */
class PhotoPersonne extends Component
{
    use WithFileUploads, VerifiePermission;

    // #[Locked] : le client ne peut pas changer la personne visée.
    #[Locked]
    public string $type = '';

    #[Locked]
    public int $personneId = 0;

    public $photo;

    public function mount(string $type, int $personneId): void
    {
        abort_unless(isset(PhotoService::TYPES[$type]), 404);
        abort_unless(PhotoService::modele($type, $personneId), 404);

        $this->type = $type;
        $this->personneId = $personneId;
    }

    /** Permissions requises pour modifier la photo, selon le type de personne. */
    protected function permissionsRequises(): array
    {
        return match ($this->type) {
            'apprenant' => ['Scolarite', 'Modifier'],
            'employer'  => ['Employer', 'Modifier'],
            default     => ['Modifier'],
        };
    }

    protected function peutModifier(): bool
    {
        foreach ($this->permissionsRequises() as $slug) {
            if (empty(Permission($slug))) {
                return false;
            }
        }

        return true;
    }

    /** La photo est enregistrée dès qu'elle est choisie. */
    public function updatedPhoto(): void
    {
        $this->exigerPermissions(...$this->permissionsRequises());

        $this->validate(
            ['photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'],
            [
                'photo.image' => 'Le fichier doit être une image.',
                'photo.mimes' => 'Formats acceptés : JPG ou PNG.',
                'photo.max'   => 'La photo ne doit pas dépasser 2 Mo.',
            ]
        );

        PhotoService::enregistrer($this->type, $this->personneId, $this->photo);
        $this->reset('photo');
    }

    public function supprimer(): void
    {
        $this->exigerPermissions(...$this->permissionsRequises());

        PhotoService::supprimer($this->type, $this->personneId);
    }

    public function render()
    {
        return view('livewire.photo-personne', [
            'url'          => PhotoService::url($this->type, $this->personneId),
            'peutModifier' => $this->peutModifier(),
        ]);
    }
}
