<?php

namespace App\Services;

use App\Models\Auth\User;
use App\Models\LicenceModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\InscriptionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenceService
{
    protected ?LicenceModel $licence = null;
    protected bool $loaded = false;
    protected ?int $anneeId = null;
    protected bool $anneeLoaded = false;

    /**
     * Licence de l'année scolaire active : chaque année a son propre quota.
     * Repli : une licence active sans année (importée alors qu'aucune année
     * n'était active) reste valable tant que l'année n'a pas la sienne.
     */
    protected function licence(): ?LicenceModel
    {
        if (!$this->loaded) {
            $anneeId = $this->anneeScolaireActiveId();
            $licence = null;

            if ($anneeId) {
                $licence = LicenceModel::active()
                    ->where('annee_scolaire_id', $anneeId)
                    ->orderByDesc('ID')
                    ->first();
            }

            $this->licence = $licence ?? LicenceModel::active()
                ->whereNull('annee_scolaire_id')
                ->orderByDesc('ID')
                ->first();
            $this->loaded = true;
        }

        return $this->licence;
    }

    /**
     * Licence importée avant la création de l'année : elle est enregistrée sans
     * année, puis rattachée à la première année activée (si celle-ci n'a pas
     * déjà sa propre licence). Les années suivantes devront avoir la leur.
     */
    public function rattacherLicenceSansAnnee(int $anneeId): void
    {
        $anneeALaSienne = LicenceModel::active()->where('annee_scolaire_id', $anneeId)->exists();

        if (!$anneeALaSienne) {
            LicenceModel::active()->whereNull('annee_scolaire_id')->update(['annee_scolaire_id' => $anneeId]);
        }

        $this->loaded = false;
        $this->anneeLoaded = false;
    }

    /**
     * Enregistre une licence importée pour l'année scolaire active et remplace
     * celle de cette même année. Les licences des autres années sont conservées.
     */
    public function enregistrerLicence(array $donnees): LicenceModel
    {
        $anneeId = $this->anneeScolaireActiveId();

        $licence = DB::transaction(function () use ($donnees, $anneeId) {
            $remplacees = LicenceModel::active();

            if ($anneeId) {
                $remplacees->where(function ($q) use ($anneeId) {
                    $q->where('annee_scolaire_id', $anneeId)->orWhereNull('annee_scolaire_id');
                });
            } else {
                $remplacees->whereNull('annee_scolaire_id');
            }

            $remplacees->update(['STATUS' => 0]);

            return LicenceModel::create($donnees + [
                'STATUS'            => 1,
                'annee_scolaire_id' => $anneeId,
            ]);
        });

        $this->loaded = false;

        return $licence;
    }

    /**
     * Recalcule la clé attendue et la compare à CLE_ACTIVATION.
     * L'algorithme DOIT rester identique à celui du générateur vendeur (admin_licence.php).
     */
    protected function cleValide(LicenceModel $licence): bool
    {
        $idClient   = config('licence.id_client');
        $codeMaitre = config('licence.code_maitre');

        if (empty($idClient) || empty($codeMaitre) || empty($licence->CLE_ACTIVATION)) {
            return false;
        }

        try {
            $dateDebut = Carbon::parse($licence->DATE_DEBUT)->format('Y-m-d');
            $dateFin   = Carbon::parse($licence->DATE_FIN)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::warning('Licence : dates invalides - ' . $e->getMessage());
            return false;
        }

        $prix = (int) $licence->PRIX_VENTES;

        $selDynamique = hash('sha1', $idClient . $codeMaitre);
        $cleAttendue  = hash(
            'sha256',
            (int) $licence->INSCRIT . (int) $licence->UTILISATEUR . $dateDebut . $dateFin . $prix . $selDynamique
        );

        return hash_equals($cleAttendue, (string) $licence->CLE_ACTIVATION);
    }

    /**
     * true = licence présente, signature correcte, ET date du jour comprise
     * entre DATE_DEBUT et DATE_FIN. C'est le seul critère utilisé pour
     * bloquer l'accès global (contrôle de la période d'expiration).
     */
    public function estActive(): bool
    {
        $licence = $this->licence();

        if (!$licence) {
            return false;
        }

        if (!$this->cleValide($licence)) {
            return false;
        }

        $aujourdhui = Carbon::now()->startOfDay();
        $debut = Carbon::parse($licence->DATE_DEBUT)->startOfDay();
        $fin   = Carbon::parse($licence->DATE_FIN)->endOfDay();

        return $aujourdhui->between($debut, $fin, true);
    }

    /**
     * Nombre de jours avant expiration (négatif si déjà expirée).
     */
    public function joursRestants(): ?int
    {
        $licence = $this->licence();
        if (!$licence) {
            return null;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays(Carbon::parse($licence->DATE_FIN)->endOfDay(), false);
    }

    public function designation(): ?string
    {
        return $this->licence()?->DESIGNATION;
    }

    public function dateFin(): ?string
    {
        $licence = $this->licence();
        return $licence ? Carbon::parse($licence->DATE_FIN)->format('d/m/Y') : null;
    }

    public function dateDebut(): ?string
    {
        $licence = $this->licence();
        return $licence ? Carbon::parse($licence->DATE_DEBUT)->format('d/m/Y') : null;
    }

    public function prixVentes(): ?float
    {
        $val = $this->licence()?->PRIX_VENTES;
        return $val !== null ? (float) $val : null;
    }

    public function idClient(): ?string
    {
        return config('licence.id_client');
    }

    public function limiteInscrits(): ?int
    {
        $val = $this->licence()?->INSCRIT;
        return $val !== null ? (int) $val : null;
    }

    public function limiteUtilisateurs(): ?int
    {
        $val = $this->licence()?->UTILISATEUR;
        return $val !== null ? (int) $val : null;
    }

    /**
     * ID de l'année scolaire actuellement active (etat = 'actif'), ou null
     * si aucune année n'est marquée active.
     */
    protected function anneeScolaireActiveId(): ?int
    {
        if (!$this->anneeLoaded) {
            $id = AnneeScolaireModel::where('etat', 'actif')->value('id');
            $this->anneeId = $id ? (int) $id : null;
            $this->anneeLoaded = true;
        }

        return $this->anneeId;
    }

    public function anneeLibelle(): ?string
    {
        $anneeId = $this->anneeScolaireActiveId();

        return $anneeId ? AnneeScolaireModel::where('id', $anneeId)->value('annee') : null;
    }

    /**
     * Quota de chaque année scolaire : licence active de l'année, limite
     * d'inscrits et nombre d'inscriptions actives (etat = 1) de cette année.
     */
    public function quotasParAnnee(): array
    {
        $anneeActiveId = $this->anneeScolaireActiveId();

        return LicenceModel::active()
            ->whereNotNull('annee_scolaire_id')
            ->orderByDesc('annee_scolaire_id')
            ->get()
            ->map(fn (LicenceModel $l) => [
                'annee'        => AnneeScolaireModel::where('id', $l->annee_scolaire_id)->value('annee') ?? ('#' . $l->annee_scolaire_id),
                'en_cours'     => (int) $l->annee_scolaire_id === $anneeActiveId,
                'limite'       => (int) $l->INSCRIT,
                'utilises'     => InscriptionModel::where('annee_scolaire_id', $l->annee_scolaire_id)->where('etat', 1)->count(),
                'utilisateurs' => (int) $l->UTILISATEUR,
                'date_debut'   => Carbon::parse($l->DATE_DEBUT)->format('d/m/Y'),
                'date_fin'     => Carbon::parse($l->DATE_FIN)->format('d/m/Y'),
            ])
            ->all();
    }

    /**
     * Le quota d'élèves inscrits (limite de la licence de l'année active) ne porte que sur l'année scolaire active,
     * et uniquement sur les inscriptions actives (etat = 1) : une nouvelle
     * année repart à zéro, et les inscriptions annulées (etat = 0) ne
     * comptent pas dans le quota, comme partout ailleurs dans l'application.
     */
    public function nombreInscritsActuel(): int
    {
        $anneeId = $this->anneeScolaireActiveId();

        if (!$anneeId) {
            return 0;
        }

        return InscriptionModel::where('annee_scolaire_id', $anneeId)
            ->where('etat', 1)
            ->count();
    }

    public function nombreUtilisateursActuel(): int
    {
        return User::where('etat', 1)->count();
    }

    public function inscritDisponible(): bool
    {
        $limite = $this->limiteInscrits();
        if ($limite === null) {
            return false;
        }
        return $this->nombreInscritsActuel() < $limite;
    }

    public function utilisateurDisponible(): bool
    {
        $limite = $this->limiteUtilisateurs();
        if ($limite === null) {
            return false;
        }
        return $this->nombreUtilisateursActuel() < $limite;
    }

    public function etatComplet(): array
    {
        return [
            'annee'          => $this->anneeLibelle(),
            'designation'    => $this->designation(),
            'id_client'      => $this->idClient(),
            'date_debut'     => $this->dateDebut(),
            'date_fin'       => $this->dateFin(),
            'prix_ventes'    => $this->prixVentes(),
            'active'         => $this->estActive(),
            'jours_restants' => $this->joursRestants(),
            'inscrits' => [
                'utilises'   => $this->nombreInscritsActuel(),
                'limite'     => $this->limiteInscrits(),
                'disponible' => $this->inscritDisponible(),
            ],
            'utilisateurs' => [
                'utilises'   => $this->nombreUtilisateursActuel(),
                'limite'     => $this->limiteUtilisateurs(),
                'disponible' => $this->utilisateurDisponible(),
            ],
            'par_annee' => $this->quotasParAnnee(),
        ];
    }
}