<?php

namespace App\Livewire\Certificat;

use App\Livewire\Concerns\VerifiePermission;
use App\Models\Certificat\CertificatModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\InscriptionModel;
use App\Services\CertificatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Gestion des certificats : liste des personnes (avec l'état de leur certificat), émission,
 * duplicata / nouvelle édition, annulation, impression (voir CertificatController).
 * Classe de base : CertificatScolarite et CertificatTravail n'en changent que le type.
 */
abstract class GestionCertificat extends Component
{
    use WithPagination, VerifiePermission;

    protected $paginationTheme = 'bootstrap';

    public $recherche = '';
    public $classe = '';        // scolarité : id de la classe
    public $population = '';    // travail : enseignant | employer
    public $filtre = '';        // '' | sans (pas de certificat valide) | avec

    // Formulaire d'émission
    public bool $formulaire = false;
    public ?int $formPersonneId = null;
    public string $formPersonneType = '';
    public ?int $formRemplaceId = null;
    public $dateDebut = '';
    public $dateFin = '';
    public $fonction = '';
    public bool $libre = false;
    public $signataire = '';
    public $qualite = '';

    abstract protected function type(): string;

    public function mount(): void
    {
        $this->autoriser();

        if ($this->type() === CertificatService::TRAVAIL) {
            $populations = $this->populations();
            $demande = request()->query('population');
            $demande = is_string($demande) ? $demande : '';
            $this->population = array_key_exists($demande, $populations) ? $demande : (array_key_first($populations) ?? '');
        }

        $this->signataire = (string) config('documents.signataire', '');
        $this->qualite = (string) config('documents.qualite', '');
    }

    public function updatedRecherche(): void { $this->resetPage(); }
    public function updatedClasse(): void { $this->resetPage(); }
    public function updatedFiltre(): void { $this->resetPage(); }

    public function updatedPopulation(): void
    {
        $this->resetPage();
        $this->fermer();
    }

    /* ------------------------------------------------------------------ permissions */

    protected function permissionPersonne(string $personneType): string
    {
        return $personneType === 'employer' ? 'Employer' : 'Scolarite';
    }

    protected function autoriser(): void
    {
        if ($this->type() === CertificatService::SCOLARITE) {
            $this->exigerPermissions('Scolarite');
            return;
        }

        abort_unless(Auth::check(), 401);
        abort_if(empty(Permission('Scolarite')) && empty(Permission('Employer')), 403, 'Accès refusé.');
    }

    /** Populations que l'utilisateur peut gérer (certificats de travail). */
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
        if ($this->type() === CertificatService::SCOLARITE) {
            return 'apprenant';
        }

        abort_unless(array_key_exists($this->population, $this->populations()), 403, 'Accès refusé.');

        return $this->population;
    }

    /* ------------------------------------------------------------------ requêtes */

    /** Requête des personnes concernées (filtres appliqués) et colonne qui identifie la personne. */
    protected function requete(string $personneType, ?int $anneeId): array
    {
        $terme = is_string($this->recherche) ? trim(mb_substr($this->recherche, 0, 100)) : '';
        $like = '%' . addcslashes($terme, '\\%_') . '%';

        if ($personneType === 'apprenant') {
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

        $requete = $personneType === 'enseignant' ? EnseignantModel::query() : EmployerModel::query();
        if ($terme !== '') {
            $requete->where(fn ($q) => $q->where('matricule', 'like', $like)->orWhere('nom', 'like', $like)->orWhere('prenom', 'like', $like));
        }

        return [$requete->orderBy('nom')->orderBy('prenom'), 'id'];
    }

    /** Certificats de ce type pour ce type de personne (scolarité : limités à l'année active). */
    protected function certificatsDe(string $personneType, ?int $anneeId)
    {
        return CertificatModel::where('type', $this->type())->where('personne_type', $personneType)
            ->when($this->type() === CertificatService::SCOLARITE, fn ($q) => $q->where('annee_scolaire_id', $anneeId));
    }

    protected function filtrerParCertificat($requete, string $colonne, string $personneType, ?int $anneeId)
    {
        if (!in_array($this->filtre, ['sans', 'avec'], true)) {
            return $requete;
        }

        $valides = $this->certificatsDe($personneType, $anneeId)->where('statut', 'valide')->select('personne_id');

        return $this->filtre === 'sans' ? $requete->whereNotIn($colonne, $valides) : $requete->whereIn($colonne, $valides);
    }

    /* ------------------------------------------------------------------ actions */

    protected function certificat(int $id): CertificatModel
    {
        $certificat = CertificatModel::where('type', $this->type())->findOrFail($id);
        $this->exigerPermissions($this->permissionPersonne($certificat->personne_type));

        return $certificat;
    }

    /** Ouvre le formulaire pour une personne sans certificat valide. */
    public function ouvrir(int $personneId): void
    {
        $personneType = $this->personneType();
        $this->exigerPermissions($this->permissionPersonne($personneType), 'Ajouter');

        $this->reinitialiserFormulaire();
        $this->formulaire = true;
        $this->formPersonneId = $personneId;
        $this->formPersonneType = $personneType;

        if ($this->type() === CertificatService::TRAVAIL) {
            $defaut = app(CertificatService::class)->valeursParDefaut($personneType, $personneId);
            $this->dateDebut = $defaut['date_debut'];
            $this->dateFin = $defaut['date_fin'] ?? '';
            $this->fonction = $defaut['fonction'];
        }
    }

    /** Duplicata / nouvelle édition : formulaire prérempli, l'ancien certificat sera remplacé. */
    public function ouvrirDuplicata(int $certificatId): void
    {
        $ancien = $this->certificat($certificatId);
        $this->exigerPermissions('Ajouter');

        $this->reinitialiserFormulaire();
        $this->formulaire = true;
        $this->formPersonneId = (int) $ancien->personne_id;
        $this->formPersonneType = $ancien->personne_type;
        $this->formRemplaceId = (int) $ancien->id;
        $this->dateDebut = $ancien->date_debut?->toDateString() ?? '';
        $this->dateFin = $ancien->date_fin?->toDateString() ?? '';
        $this->fonction = (string) $ancien->fonction;
        $this->libre = (bool) $ancien->libre_engagement;
        $this->signataire = (string) ($ancien->signataire ?? '');
        $this->qualite = (string) ($ancien->qualite_signataire ?? '');
    }

    public function fermer(): void
    {
        $this->reinitialiserFormulaire();
    }

    protected function reinitialiserFormulaire(): void
    {
        $this->formulaire = false;
        $this->formPersonneId = null;
        $this->formPersonneType = '';
        $this->formRemplaceId = null;
        $this->dateDebut = '';
        $this->dateFin = '';
        $this->fonction = '';
        $this->libre = false;
        $this->signataire = (string) config('documents.signataire', '');
        $this->qualite = (string) config('documents.qualite', '');
    }

    public function enregistrer(): void
    {
        $this->exigerPermissions('Ajouter');

        if (!$this->formulaire || !$this->formPersonneId) {
            return;
        }

        $ancien = $this->formRemplaceId ? $this->certificat($this->formRemplaceId) : null;
        $this->exigerPermissions($this->permissionPersonne($this->formPersonneType));

        try {
            $certificat = app(CertificatService::class)->emettre($this->type(), $this->formPersonneType, $this->formPersonneId, [
                'date_debut'         => $this->dateDebut,
                'date_fin'           => $this->dateFin,
                'fonction'           => $this->fonction,
                'libre_engagement'   => $this->libre,
                'signataire'         => $this->signataire,
                'qualite_signataire' => $this->qualite,
            ], $ancien);

            session()->flash('success', 'Certificat ' . $certificat->numero . ' généré' . ($ancien ? ' (remplace ' . $ancien->numero . ').' : '.'));
            $this->fermer();
        } catch (\RuntimeException $e) {
            session()->flash('danger', $e->getMessage());
        }
    }

    public function annuler(int $certificatId): void
    {
        $certificat = $this->certificat($certificatId);
        $this->exigerPermissions('Modifier');

        if ($certificat->statut === 'valide') {
            $certificat->update(['statut' => 'annule']);
            session()->flash('warning', 'Certificat ' . $certificat->numero . ' annulé.');
        }
    }

    /* ------------------------------------------------------------------ affichage */

    public function render()
    {
        $this->autoriser();

        $anneeId = AnneeScolaire()?->id;
        $personneType = $this->personneType();

        [$requete, $colonne] = $this->requete($personneType, $anneeId);
        $requete = $this->filtrerParCertificat($requete, $colonne, $personneType, $anneeId);
        $page = $requete->paginate(20);

        // Dernier certificat de chaque personne de la page
        $certificats = $this->certificatsDe($personneType, $anneeId)
            ->whereIn('personne_id', $page->getCollection()->pluck($colonne))
            ->orderBy('id')->get()->groupBy('personne_id')->map(fn ($g) => $g->last());

        $lignes = $page->through(function ($p) use ($personneType, $colonne, $certificats) {
            $id = $p->{$colonne};
            if ($personneType === 'apprenant') {
                return [
                    'id' => $id, 'matricule' => $p->matricule,
                    'nom' => $p->preInscription?->nom, 'prenom' => $p->preInscription?->prenom,
                    'detail' => $p->classe?->classe, 'certificat' => $certificats->get($id),
                ];
            }

            return [
                'id' => $id, 'matricule' => $p->matricule, 'nom' => $p->nom, 'prenom' => $p->prenom,
                'detail' => $personneType === 'enseignant' ? $p->specialite : $p->poste, 'certificat' => $certificats->get($id),
            ];
        });

        $scolarite = $this->type() === CertificatService::SCOLARITE;

        return view('livewire.certificat.gestion-certificat', [
            'scolarite'    => $scolarite,
            'titre'        => $scolarite ? 'Certificats de scolarité' : 'Certificats de travail',
            'lignes'       => $lignes,
            'classes'      => $scolarite ? ClasseEtudeModel::orderBy('classe')->get() : collect(),
            'populations'  => $this->populations(),
            // scolarité : année active obligatoire ; travail : pas nécessaire
            'anneeActive'  => !$scolarite || (bool) $anneeId,
            'peutAjouter'  => !empty(Permission('Ajouter')),
            'peutModifier' => !empty(Permission('Modifier')),
        ]);
    }
}
