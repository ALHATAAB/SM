<?php

namespace App\Services;

use App\Models\Certificat\CertificatModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\PreInscriptionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Certificats de scolarité (élèves) et certificats de travail (enseignants, personnel) :
 * émission, numérotation, données d'impression.
 */
class CertificatService
{
    public const SCOLARITE = 'scolarite';
    public const TRAVAIL = 'travail';

    /** Types de personne autorisés pour chaque type de certificat. */
    public const PERSONNES = [
        self::SCOLARITE => ['apprenant'],
        self::TRAVAIL   => ['enseignant', 'employer'],
    ];

    public const STATUTS = [
        'valide'   => 'Valide',
        'annule'   => 'Annulé',
        'remplace' => 'Remplacé',
    ];

    /**
     * Émet un certificat. Avec $remplace : duplicata, le certificat précédent passe à « remplace »
     * (il reste « annule » s'il l'était déjà).
     *
     * Options : date_debut, date_fin, fonction, libre_engagement, signataire, qualite_signataire.
     *
     * @throws \RuntimeException message affichable à l'utilisateur
     */
    public function emettre(string $type, string $personneType, int $personneId, array $options = [], ?CertificatModel $remplace = null): CertificatModel
    {
        if (!in_array($personneType, self::PERSONNES[$type] ?? [], true)) {
            throw new \RuntimeException('Type de certificat invalide pour cette personne.');
        }

        $annee = AnneeScolaire();

        if ($type === self::SCOLARITE) {
            if (!$annee) {
                throw new \RuntimeException('Aucune année scolaire active.');
            }
            $this->verifierEleve($personneId, (int) $annee->id);
        } else {
            $this->verifierPersonnel($personneType, $personneId);
        }

        $champs = $this->champs($type, $options);

        return DB::transaction(function () use ($type, $personneType, $personneId, $remplace, $annee, $champs) {
            if ($remplace) {
                if ($remplace->statut === 'valide') {
                    $remplace->update(['statut' => 'remplace']);
                }
            } else {
                $existe = CertificatModel::where('type', $type)
                    ->where('personne_type', $personneType)
                    ->where('personne_id', $personneId)
                    ->where('statut', 'valide')
                    ->when($type === self::SCOLARITE, fn ($q) => $q->where('annee_scolaire_id', $annee->id))
                    ->lockForUpdate()
                    ->exists();

                if ($existe) {
                    throw new \RuntimeException('Un certificat valide existe déjà pour cette personne : utilisez « Duplicata / nouvelle édition ».');
                }
            }

            $certificat = CertificatModel::create($champs + [
                'type'              => $type,
                'personne_type'     => $personneType,
                'personne_id'       => $personneId,
                'numero'            => $this->prochainNumero($type, $type === self::SCOLARITE ? (string) $annee->annee : (string) now()->year),
                'annee_scolaire_id' => $annee?->id,
                'date_emission'     => Carbon::today(),
                'statut'            => 'valide',
                'duplicata_de'      => $remplace?->id,
                'user_id'           => Auth::id(),
            ]);

            // La date de fin saisie sur un certificat de travail est la date de départ de l'employé / enseignant
            // (laissée vide : on ne touche à rien, l'employé reste « en poste »).
            if ($type === self::TRAVAIL && !empty($champs['date_fin'])) {
                $modele = $personneType === 'enseignant' ? EnseignantModel::class : EmployerModel::class;
                $modele::whereKey($personneId)->update(['date_depart' => $champs['date_fin']->toDateString()]);
            }

            return $certificat;
        });
    }

    /** Nettoie et contrôle les options saisies dans le formulaire. */
    protected function champs(string $type, array $o): array
    {
        $texte = fn ($v, int $max) => ($v === null || trim((string) $v) === '') ? null : mb_substr(trim((string) $v), 0, $max);

        $champs = [
            'signataire'         => $texte($o['signataire'] ?? null, 120),
            'qualite_signataire' => $texte($o['qualite_signataire'] ?? null, 120),
        ];

        if ($type === self::SCOLARITE) {
            return $champs;
        }

        $debut = $this->dateOuNull($o['date_debut'] ?? null);
        $fin = $this->dateOuNull($o['date_fin'] ?? null);

        if (!$debut) {
            throw new \RuntimeException("La date de prise de fonction est obligatoire.");
        }
        if ($fin && $fin->lt($debut)) {
            throw new \RuntimeException('La date de fin ne peut pas précéder la date de prise de fonction.');
        }

        $fonction = $texte($o['fonction'] ?? null, 150);
        if (!$fonction) {
            throw new \RuntimeException('La fonction occupée est obligatoire.');
        }

        return $champs + [
            'date_debut'       => $debut,
            'date_fin'         => $fin,
            'fonction'         => $fonction,
            'libre_engagement' => $fin ? (bool) ($o['libre_engagement'] ?? false) : false,
        ];
    }

    protected function dateOuNull($valeur): ?Carbon
    {
        if (empty($valeur)) {
            return null;
        }

        try {
            return Carbon::parse($valeur)->startOfDay();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Date invalide.');
        }
    }

    protected function verifierEleve(int $id, int $anneeId): void
    {
        if (!PreInscriptionModel::whereKey($id)->exists()) {
            throw new \RuntimeException('Personne introuvable.');
        }

        if (!InscriptionModel::where('pre_inscription_id', $id)->where('annee_scolaire_id', $anneeId)->where('etat', 1)->exists()) {
            throw new \RuntimeException("Cet élève n'est pas inscrit pour l'année scolaire active.");
        }
    }

    protected function verifierPersonnel(string $type, int $id): void
    {
        $existe = $type === 'enseignant' ? EnseignantModel::whereKey($id)->exists() : EmployerModel::whereKey($id)->exists();

        if (!$existe) {
            throw new \RuntimeException('Personne introuvable.');
        }
    }

    /** CS-2026-2027-00001 (scolarité, année scolaire) / CT-2026-00001 (travail, année civile). */
    protected function prochainNumero(string $type, string $annee): string
    {
        $prefixe = ($type === self::SCOLARITE ? 'CS' : 'CT') . '-' . preg_replace('/[^0-9A-Za-z-]+/', '-', $annee) . '-';

        $dernier = CertificatModel::where('numero', 'like', $prefixe . '%')->lockForUpdate()->orderByDesc('numero')->value('numero');
        $suite = $dernier ? ((int) substr($dernier, strlen($prefixe))) + 1 : 1;

        return $prefixe . str_pad((string) $suite, 5, '0', STR_PAD_LEFT);
    }

    /** Valeurs proposées dans le formulaire d'un certificat de travail. */
    public function valeursParDefaut(string $personneType, int $personneId): array
    {
        if ($personneType === 'employer') {
            $e = EmployerModel::find($personneId);

            return [
                'date_debut' => $e?->date_embauche ? Carbon::parse($e->date_embauche)->toDateString() : '',
                'date_fin'   => $e?->date_depart ? Carbon::parse($e->date_depart)->toDateString() : '',
                'fonction'   => (string) ($e?->poste ?? ''),
            ];
        }

        $e = EnseignantModel::find($personneId);
        $fonction = 'Enseignant' . (!empty($e?->specialite) ? ' (' . $e->specialite . ')' : '');

        return [
            'date_debut' => ($e?->date_embauche ?? $e?->created_at) ? Carbon::parse($e->date_embauche ?? $e->created_at)->toDateString() : '',
            'date_fin'   => $e?->date_depart ? Carbon::parse($e->date_depart)->toDateString() : '',
            'fonction'   => $fonction,
        ];
    }

    /* ------------------------------------------------------------------ impression */

    /** Données prêtes à imprimer pour un certificat. */
    public function donnees(CertificatModel $c): array
    {
        $donnees = [
            'certificat' => $c,
            'ecole'      => SocieteService::info(),
            'pays'       => config('documents.entete_pays'),
            'devise'     => config('documents.devise_pays'),
            'ville'      => config('documents.ville'),
            'duplicata'  => $c->estDuplicata(),
        ];

        if ($c->type === self::SCOLARITE) {
            $pre = PreInscriptionModel::find($c->personne_id);
            $inscription = InscriptionModel::with(['niveau', 'filiere', 'classe', 'annee'])
                ->where('pre_inscription_id', $c->personne_id)
                ->where('annee_scolaire_id', $c->annee_scolaire_id)
                ->where('etat', 1)->latest('id')->first();

            return $donnees + [
                'titre'     => 'Certificat de scolarité',
                'personne'  => $this->personne($pre),
                'annee'     => $inscription?->annee?->annee,
                'niveau'    => $inscription?->niveau?->niveau,
                'filiere'   => $inscription?->filiere?->filiere,
                'classe'    => $inscription?->classe?->classe,
            ];
        }

        $personne = $c->personne_type === 'enseignant' ? EnseignantModel::find($c->personne_id) : EmployerModel::find($c->personne_id);

        return $donnees + [
            'titre'    => 'Certificat de travail',
            'personne' => $this->personne($personne),
        ];
    }

    /** Identité d'une personne (élève, enseignant ou employé) telle qu'imprimée. */
    protected function personne($p): array
    {
        $sexe = mb_strtolower(trim((string) ($p?->sexe ?? '')));

        return [
            'nom'       => mb_strtoupper((string) ($p?->nom ?? '')),
            'prenom'    => (string) ($p?->prenom ?? ''),
            'matricule' => (string) ($p?->matricule ?? ''),
            'feminin'   => in_array($sexe, ['feminin', 'féminin', 'f', 'femme', 'fille'], true),
            'naissance' => $p?->date_naissance ? Carbon::parse($p->date_naissance)->format('d/m/Y') : '',
            'lieu'      => (string) ($p?->lieu_naissance ?? ''),
        ];
    }
}
