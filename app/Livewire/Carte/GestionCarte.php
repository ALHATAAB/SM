<?php

namespace App\Livewire\Carte;

use App\Livewire\Concerns\VerifiePermission;
use App\Models\Carte\CarteModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\InscriptionModel;
use App\Services\CarteService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Gestion des cartes : liste des personnes (avec l'état de leur carte), émission, duplicata,
 * déclaration de perte, annulation, impression (voir CarteController).
 * Classe de base : CarteScolaire et CartePersonnel n'en changent que la catégorie.
 */
abstract class GestionCarte extends Component
{
    use WithPagination, VerifiePermission;

    protected $paginationTheme = 'bootstrap';

    public $recherche = '';
    public $classe = '';        // cartes scolaires : id de la classe
    public $population = '';    // cartes professionnelles : enseignant | employer
    public $filtre = '';        // '' | sans (pas de carte valide) | avec

    abstract protected function categorie(): string;

    public function mount(): void
    {
        $this->autoriser();

        if ($this->categorie() === CarteService::PROFESSIONNELLE) {
            $this->population = array_key_first($this->populations()) ?? '';
        }
    }

    public function updatedRecherche(): void { $this->resetPage(); }
    public function updatedClasse(): void { $this->resetPage(); }
    public function updatedPopulation(): void { $this->resetPage(); }
    public function updatedFiltre(): void { $this->resetPage(); }

    /* ------------------------------------------------------------------ permissions */

    /** Permission de consultation d'un type de personne. */
    protected function permissionPersonne(string $type): string
    {
        return $type === 'employer' ? 'Employer' : 'Scolarite';
    }

    protected function autoriser(): void
    {
        if ($this->categorie() === CarteService::SCOLAIRE) {
            $this->exigerPermissions('Scolarite');
            return;
        }

        abort_unless(\Illuminate\Support\Facades\Auth::check(), 401);
        abort_if(empty(Permission('Scolarite')) && empty(Permission('Employer')), 403, 'Accès refusé.');
    }

    /** Populations que l'utilisateur peut gérer (cartes professionnelles). */
    public function populations(): array
    {
        $liste = [];
        if (!empty(Permission('Scolarite'))) {
            $liste['enseignant'] = 'Enseignants';
        }
        if (!empty(Permission('Employer'))) {
            $liste['employer'] = 'Personnel administratif';
        }

        return $liste;
    }

    protected function personneType(): string
    {
        if ($this->categorie() === CarteService::SCOLAIRE) {
            return 'apprenant';
        }

        abort_unless(array_key_exists($this->population, $this->populations()), 403, 'Accès refusé.');

        return $this->population;
    }

    /* ------------------------------------------------------------------ requêtes */

    /** Requête des personnes concernées (filtres appliqués) et colonne qui identifie la personne. */
    protected function requete(string $type, ?int $anneeId): array
    {
        $terme = is_string($this->recherche) ? trim(mb_substr($this->recherche, 0, 100)) : '';
        $like  = '%' . addcslashes($terme, '\\%_') . '%';

        if ($type === 'apprenant') {
            $requete = InscriptionModel::query()->with(['preInscription', 'classe'])
                ->where('annee_scolaire_id', $anneeId)->where('etat', 1)
                // une seule ligne par élève (sa dernière inscription de l'année)
                ->whereIn('id', fn ($q) => $q->from('inscription_models')->selectRaw('MAX(id)')
                    ->where('annee_scolaire_id', $anneeId)->where('etat', 1)->groupBy('pre_inscription_id'));

            if ($this->classe !== '' && $this->classe !== null) {
                $requete->where('classe_etude_id', (int) $this->classe);
            }
            if ($terme !== '') {
                $requete->where(fn ($q) => $q->where('matricule', 'like', $like)
                    ->orWhereHas('preInscription', fn ($s) => $s->where('nom', 'like', $like)->orWhere('prenom', 'like', $like)));
            }

            return [$requete->orderByDesc('id'), 'pre_inscription_id'];
        }

        $requete = $type === 'enseignant' ? EnseignantModel::query() : EmployerModel::query();
        if ($terme !== '') {
            $requete->where(fn ($q) => $q->where('matricule', 'like', $like)->orWhere('nom', 'like', $like)->orWhere('prenom', 'like', $like));
        }

        return [$requete->orderBy('nom')->orderBy('prenom'), 'id'];
    }

    /** Ajoute le filtre « avec / sans carte valide » à la requête. */
    protected function filtrerParCarte($requete, string $colonne, string $type, ?int $anneeId)
    {
        if (!in_array($this->filtre, ['sans', 'avec'], true)) {
            return $requete;
        }

        $valides = CarteModel::select('personne_id')
            ->where('categorie', $this->categorie())->where('personne_type', $type)
            ->where('annee_scolaire_id', $anneeId)->where('statut', 'active')
            ->where('date_expiration', '>=', now()->toDateString());

        return $this->filtre === 'sans' ? $requete->whereNotIn($colonne, $valides) : $requete->whereIn($colonne, $valides);
    }

    /* ------------------------------------------------------------------ actions */

    protected function carte(int $id): CarteModel
    {
        $carte = CarteModel::where('categorie', $this->categorie())->findOrFail($id);
        $this->exigerPermissions($this->permissionPersonne($carte->personne_type));

        return $carte;
    }

    public function generer(int $personneId): void
    {
        $type = $this->personneType();
        $this->exigerPermissions($this->permissionPersonne($type), 'Ajouter');

        try {
            $carte = app(CarteService::class)->emettre($this->categorie(), $type, $personneId);
            session()->flash('success', 'Carte ' . $carte->numero . ' générée.');
        } catch (\RuntimeException $e) {
            session()->flash('danger', $e->getMessage());
        }
    }

    /** Duplicata : nouvelle carte, l'ancienne est remplacée. */
    public function remplacer(int $carteId): void
    {
        $ancienne = $this->carte($carteId);
        $this->exigerPermissions('Ajouter');

        try {
            $carte = app(CarteService::class)->emettre($ancienne->categorie, $ancienne->personne_type, $ancienne->personne_id, $ancienne);
            session()->flash('success', 'Nouvelle carte ' . $carte->numero . ' générée (remplace ' . $ancienne->numero . ').');
        } catch (\RuntimeException $e) {
            session()->flash('danger', $e->getMessage());
        }
    }

    public function declarerPerdue(int $carteId): void
    {
        $carte = $this->carte($carteId);
        $this->exigerPermissions('Modifier');

        if ($carte->statut === 'active') {
            $carte->update(['statut' => 'perdue']);
            session()->flash('warning', 'Carte ' . $carte->numero . ' déclarée perdue : elle n\'est plus valable.');
        }
    }

    public function annuler(int $carteId): void
    {
        $carte = $this->carte($carteId);
        $this->exigerPermissions('Modifier');

        if ($carte->statut === 'active') {
            $carte->update(['statut' => 'annulee']);
            session()->flash('warning', 'Carte ' . $carte->numero . ' annulée.');
        }
    }

    /** Génère toutes les cartes manquantes pour le filtre courant (classe ou population). */
    public function genererManquantes(): void
    {
        $type = $this->personneType();
        $this->exigerPermissions($this->permissionPersonne($type), 'Ajouter');

        $anneeId = AnneeScolaire()?->id;
        if (!$anneeId) {
            session()->flash('danger', 'Aucune année scolaire active.');
            return;
        }

        [$requete, $colonne] = $this->requete($type, $anneeId);
        $requete = $this->filtrerParCarte($requete, $colonne, $type, $anneeId);

        // Seules les personnes sans carte valide
        $valides = CarteModel::select('personne_id')
            ->where('categorie', $this->categorie())->where('personne_type', $type)
            ->where('annee_scolaire_id', $anneeId)->where('statut', 'active');
        $requete->whereNotIn($colonne, $valides);

        $service = app(CarteService::class);
        $generees = $echecs = 0;

        foreach ($requete->pluck($colonne) as $personneId) {
            try {
                $service->emettre($this->categorie(), $type, (int) $personneId);
                $generees++;
            } catch (\RuntimeException $e) {
                $echecs++;
            }
        }

        session()->flash($generees ? 'success' : 'info', $generees . ' carte(s) générée(s)' . ($echecs ? ", {$echecs} ignorée(s)." : '.'));
    }

    /* ------------------------------------------------------------------ affichage */

    public function render()
    {
        $this->autoriser();

        $anneeId = AnneeScolaire()?->id;
        $type = $this->personneType();

        [$requete, $colonne] = $this->requete($type, $anneeId);
        $requete = $this->filtrerParCarte($requete, $colonne, $type, $anneeId);
        $page = $requete->paginate(20);

        // Dernière carte de chaque personne de la page, pour l'année active
        $cartes = CarteModel::where('categorie', $this->categorie())->where('personne_type', $type)
            ->where('annee_scolaire_id', $anneeId)
            ->whereIn('personne_id', $page->getCollection()->pluck($colonne))
            ->orderBy('id')->get()->groupBy('personne_id')->map(fn ($g) => $g->last());

        $lignes = $page->through(function ($p) use ($type, $colonne, $cartes) {
            $id = $p->{$colonne};
            if ($type === 'apprenant') {
                return [
                    'id' => $id, 'matricule' => $p->matricule,
                    'nom' => $p->preInscription?->nom, 'prenom' => $p->preInscription?->prenom,
                    'detail' => $p->classe?->classe, 'carte' => $cartes->get($id),
                ];
            }

            return [
                'id' => $id, 'matricule' => $p->matricule, 'nom' => $p->nom, 'prenom' => $p->prenom,
                'detail' => $type === 'enseignant' ? $p->specialite : $p->poste, 'carte' => $cartes->get($id),
            ];
        });

        return view('livewire.carte.gestion-carte', [
            'categorie'   => $this->categorie(),
            'type'        => $type,
            'titre'       => $this->categorie() === CarteService::SCOLAIRE ? 'Cartes scolaires' : 'Cartes professionnelles',
            'lignes'      => $lignes,
            'classes'     => $this->categorie() === CarteService::SCOLAIRE ? ClasseEtudeModel::orderBy('classe')->get() : collect(),
            'populations' => $this->populations(),
            'anneeActive' => (bool) $anneeId,
            'peutAjouter' => !empty(Permission('Ajouter')),
            'peutModifier' => !empty(Permission('Modifier')),
        ]);
    }
}
