<?php

namespace App\Livewire\Scolarite;

use App\Livewire\Concerns\VerifiePermission;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\InscriptionModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class ListeInscrit extends Component
{
    use WithPagination, VerifiePermission;

    protected $paginationTheme = 'bootstrap';

    public $recherche = '';
    public $classe;

    // #[Locked] : le client ne peut PAS modifier cet id (sinon il pourrait
    // affecter une classe à n'importe quelle inscription via la console du navigateur).
    #[Locked]
    public $inscription_id;

    public function mount()
    {
        // Même règle que le menu latéral (Scolarité > Liste des inscrits)
        $this->exigerPermissions('Scolarite');
    }

    public function updatedRecherche()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Rappel : Livewire n'exécute pas mount() aux requêtes suivantes -> on revérifie.
        $this->exigerPermissions('Scolarite');

        $classes = ClasseEtudeModel::orderBy('classe', 'ASC')->get();
        $anneeId = AnneeScolaire()?->id;

        $query = InscriptionModel::query()
            ->with(['preInscription', 'niveau', 'filiere', 'classe', 'annee'])
            ->orderByDesc('id');

        if ($anneeId) {
            $query->where('annee_scolaire_id', $anneeId)->where('etat', 1);
        } else {
            // Aucune année scolaire active : on n'affiche rien (au lieu de "annee_scolaire_id IS NULL")
            $query->whereRaw('1 = 0');
        }

        // Recherche : valeur bornée, et jokers LIKE (% _) neutralisés
        $terme = is_string($this->recherche) ? trim(mb_substr($this->recherche, 0, 100)) : '';

        if ($terme !== '') {
            $like = '%' . addcslashes($terme, '\\%_') . '%';

            $query->where(function ($q) use ($like) {
                $q->where('matricule', 'like', $like)
                    ->orWhereHas('preInscription', function ($sub) use ($like) {
                        $sub->where('nom', 'like', $like)
                            ->orWhere('prenom', 'like', $like)
                            ->orWhere('telephone', 'like', $like);
                    })
                    ->orWhereHas('niveau', fn ($sub) => $sub->where('niveau', 'like', $like))
                    ->orWhereHas('filiere', fn ($sub) => $sub->where('filiere', 'like', $like));
            });
        }

        return view('livewire.scolarite.liste-inscrit', [
            'inscrits'    => $query->paginate(25),
            'classes'     => $classes,
            'anneeActive' => (bool) $anneeId,
        ]);
    }

    /** Inscription de l'année active, non annulée ; 404 sinon. */
    private function inscriptionModifiable($id): InscriptionModel
    {
        $anneeId = AnneeScolaire()?->id;

        abort_if(!$anneeId || !is_numeric($id), 404);

        return InscriptionModel::where('annee_scolaire_id', $anneeId)
            ->where('etat', 1)
            ->findOrFail((int) $id);
    }

    public function affectationClasse($id)
    {
        $this->exigerPermissions('Scolarite', 'Modifier');

        $inscription = $this->inscriptionModifiable($id);

        $this->resetErrorBag();
        $this->inscription_id = $inscription->id;
        $this->classe = $inscription->classe_etude_id;
    }

    public function updateAffectationClasse()
    {
        $this->exigerPermissions('Scolarite', 'Modifier');

        $inscription = $this->inscriptionModifiable($this->inscription_id);

        $validated = $this->validate([
            // la classe doit réellement exister (et pas une valeur arbitraire envoyée par le client)
            'classe' => ['required', 'integer', 'exists:classe_etude_models,id'],
        ]);

        $ancienne = $inscription->classe_etude_id;

        $inscription->update([
            'classe_etude_id' => $validated['classe'],
        ]);

        // Traçabilité : qui a changé quoi
        Log::info('Affectation de classe modifiée', [
            'user_id'        => Auth::id(),
            'inscription_id' => $inscription->id,
            'ancienne'       => $ancienne,
            'nouvelle'       => $validated['classe'],
            'ip'             => request()->ip(),
        ]);

        return redirect('/liste/inscrit')->with('info', 'Modification effectuée avec succès');
    }
}
