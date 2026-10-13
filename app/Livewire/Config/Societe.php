<?php

namespace App\Livewire\Config;

use App\Models\Config\SocieteModel;
use App\Services\SocieteService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Societe extends Component
{
    use WithFileUploads;

    public ?int $societe_id = null;

    public $raison_sociale;
    public $activite;
    public $nif;
    public $rccm;
    public $forme_juridique;
    public $telephone;
    public $email;
    public $code_postal;
    public $adresse;
    public $complement;
    public $site_web;

    /** Nouveau logo sélectionné (fichier temporaire, pas encore enregistré). */
    public $logo;

    /** Chemin (sur le disque "public") du logo déjà enregistré. */
    public $logo_actuel;

    protected function rules(): array
    {
        return [
            'raison_sociale'  => 'required|string|max:191',
            'activite'        => 'nullable|string|max:191',
            'nif'             => 'nullable|string|max:100',
            'rccm'            => 'nullable|string|max:100',
            'forme_juridique' => 'nullable|string|max:191',
            'telephone'       => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:191',
            'code_postal'     => 'nullable|string|max:20',
            'adresse'         => 'nullable|string|max:191',
            'complement'      => 'nullable|string|max:191',
            'site_web'        => 'nullable|url|max:191',
            'logo'            => 'nullable|image|max:2048',
        ];
    }

    public function mount(): void
    {
        // La société est un singleton : une seule ligne pour tout l'établissement.
        $societe = SocieteModel::first();

        if ($societe) {
            $this->societe_id     = $societe->id;
            $this->raison_sociale = $societe->raison_sociale;
            $this->activite       = $societe->activite;
            $this->nif            = $societe->nif;
            $this->rccm           = $societe->rccm;
            $this->forme_juridique = $societe->forme_juridique;
            $this->telephone      = $societe->telephone;
            $this->email          = $societe->email;
            $this->code_postal    = $societe->code_postal;
            $this->adresse        = $societe->adresse;
            $this->complement     = $societe->complement;
            $this->site_web       = $societe->site_web;
            $this->logo_actuel    = $societe->logo;
        }
    }

    public function save()
    {
        $validated = $this->validate();

        // Le logo n'est validé/inclus que s'il vient d'être choisi ;
        // sinon on garde le fichier déjà enregistré.
        unset($validated['logo']);

        if ($this->logo) {
            // Supprime l'ancien logo pour ne pas accumuler des fichiers orphelins.
            if ($this->logo_actuel && Storage::disk('public')->exists($this->logo_actuel)) {
                Storage::disk('public')->delete($this->logo_actuel);
            }

            $validated['logo'] = $this->logo->store('societe', 'public');
        }

        $societe = SocieteModel::updateOrCreate(
            ['id' => $this->societe_id],
            $validated
        );

        SocieteService::oublier();

        $this->societe_id  = $societe->id;
        $this->logo_actuel = $societe->logo;
        $this->reset('logo');

        session()->flash('success', "Paramètres de la société enregistrés avec succès.");
    }

    public function render()
    {
        return view('livewire.config.societe');
    }
}
