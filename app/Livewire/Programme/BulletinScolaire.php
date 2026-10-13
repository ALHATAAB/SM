<?php

namespace App\Livewire\Programme;

use App\Livewire\Concerns\VerifiePermission;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\TrimestreModel;
use App\Services\BulletinService;
use Livewire\Component;

/**
 * Bulletins scolaires d'une classe (année scolaire active) : par trimestre
 * ou en synthèse annuelle, à l'écran puis en PDF (un élève ou toute la classe).
 */
class BulletinScolaire extends Component
{
    use VerifiePermission;

    public $classes = [];
    public $trimestres = [];
    public $classe_id = '';
    public $periode = '';
    public $anneeActive = true;

    public function mount()
    {
        $this->exigerPermissions('Programme');

        $annee = AnneeScolaire();

        if (!$annee) {
            $this->anneeActive = false;
            return;
        }

        $this->classes = ClasseEtudeModel::orderBy('classe')->get();
        $this->trimestres = TrimestreModel::where('annee_scolaire_id', $annee->id)
            ->orderBy('date_debut')->orderBy('id')->get();
    }

    /**
     * Calcule les bulletins de la sélection courante (null tant qu'elle est incomplète).
     */
    protected function donnees(): ?array
    {
        $annee = AnneeScolaire();

        if (!$annee || !$this->classe_id || $this->periode === '' || $this->periode === null) {
            return null;
        }

        $classeId = (int) $this->classe_id;
        abort_unless(ClasseEtudeModel::whereKey($classeId)->exists(), 404);

        $periode = $this->periode === BulletinService::ANNUEL ? BulletinService::ANNUEL : (int) $this->periode;

        return app(BulletinService::class)->classe($classeId, $annee->id, $periode);
    }

    /**
     * Adresse d'impression (ouverte dans un nouvel onglet) : toute la classe,
     * ou un seul élève si $inscriptionId est fourni.
     */
    public function urlImpression($inscriptionId = null): string
    {
        $parametres = ['classe' => (int) $this->classe_id, 'periode' => $this->periode];

        if ($inscriptionId !== null) {
            $parametres['eleve'] = (int) $inscriptionId;
        }

        return url('bulletin/scolaire/imprimer') . '?' . http_build_query($parametres);
    }

    public function render()
    {
        return view('livewire.programme.bulletin-scolaire', [
            'resultat' => $this->donnees(),
        ]);
    }
}
