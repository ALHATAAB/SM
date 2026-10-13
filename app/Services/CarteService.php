<?php

namespace App\Services;

use App\Models\Carte\CarteModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\PreInscriptionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cartes scolaires (élèves) et cartes professionnelles (enseignants, personnel) :
 * émission, numérotation, données d'impression.
 */
class CarteService
{
    public const SCOLAIRE = 'scolaire';
    public const PROFESSIONNELLE = 'professionnelle';

    /** Types de personne autorisés pour chaque catégorie de carte. */
    public const PERSONNES = [
        self::SCOLAIRE        => ['apprenant'],
        self::PROFESSIONNELLE => ['enseignant', 'employer'],
    ];

    public const STATUTS = [
        'active'    => 'Active',
        'expiree'   => 'Expirée',
        'perdue'    => 'Perdue',
        'annulee'   => 'Annulée',
        'remplacee' => 'Remplacée',
    ];

    /**
     * Émet une carte. Avec $remplace : duplicata, la carte précédente est marquée « remplacee »
     * (elle reste « perdue » ou « annulee » si elle l'était déjà).
     *
     * @throws \RuntimeException message affichable à l'utilisateur
     */
    public function emettre(string $categorie, string $personneType, int $personneId, ?CarteModel $remplace = null): CarteModel
    {
        if (!in_array($personneType, self::PERSONNES[$categorie] ?? [], true)) {
            throw new \RuntimeException('Type de carte invalide pour cette personne.');
        }

        $annee = AnneeScolaire();
        if (!$annee) {
            throw new \RuntimeException("Aucune année scolaire active.");
        }

        $this->verifierPersonne($personneType, $personneId, (int) $annee->id);

        return DB::transaction(function () use ($categorie, $personneType, $personneId, $remplace, $annee) {
            if ($remplace) {
                if ($remplace->statut === 'active') {
                    $remplace->update(['statut' => 'remplacee']);
                }
            } else {
                $existe = CarteModel::where('categorie', $categorie)
                    ->where('personne_type', $personneType)
                    ->where('personne_id', $personneId)
                    ->where('annee_scolaire_id', $annee->id)
                    ->where('statut', 'active')
                    ->lockForUpdate()
                    ->exists();

                if ($existe) {
                    throw new \RuntimeException('Une carte active existe déjà pour cette personne : utilisez « Duplicata ».');
                }
            }

            $emission = Carbon::today();
            $fin = $annee->date_fin ? Carbon::parse($annee->date_fin) : null;
            $expiration = ($fin && $fin->gte($emission)) ? $fin : $emission->copy()->addYear();

            return CarteModel::create([
                'categorie'         => $categorie,
                'personne_type'     => $personneType,
                'personne_id'       => $personneId,
                'numero'            => $this->prochainNumero($categorie, $annee->annee),
                'annee_scolaire_id' => $annee->id,
                'date_emission'     => $emission,
                'date_expiration'   => $expiration,
                'statut'            => 'active',
                'duplicata_de'      => $remplace?->id,
                'user_id'           => Auth::id(),
            ]);
        });
    }

    protected function verifierPersonne(string $type, int $id, int $anneeId): void
    {
        $existe = match ($type) {
            'apprenant'  => PreInscriptionModel::whereKey($id)->exists(),
            'enseignant' => EnseignantModel::whereKey($id)->exists(),
            'employer'   => EmployerModel::whereKey($id)->exists(),
            default      => false,
        };

        if (!$existe) {
            throw new \RuntimeException('Personne introuvable.');
        }

        if ($type === 'apprenant'
            && !InscriptionModel::where('pre_inscription_id', $id)->where('annee_scolaire_id', $anneeId)->where('etat', 1)->exists()) {
            throw new \RuntimeException("Cet élève n'est pas inscrit pour l'année scolaire active.");
        }
    }

    /** CS-2026-00001 / CP-2026-00001 : préfixe, année scolaire, compteur. */
    protected function prochainNumero(string $categorie, $annee): string
    {
        $prefixe = ($categorie === self::SCOLAIRE ? 'CS' : 'CP') . '-' . $annee . '-';

        $dernier = CarteModel::where('numero', 'like', $prefixe . '%')->lockForUpdate()->orderByDesc('numero')->value('numero');
        $suite = $dernier ? ((int) substr($dernier, strlen($prefixe))) + 1 : 1;

        return $prefixe . str_pad((string) $suite, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Cartes professionnelles valides (année scolaire active) des personnes données, indexées par personne_id.
     *
     * @param  iterable<int>  $ids
     */
    public function cartesProValides(string $personneType, iterable $ids): \Illuminate\Support\Collection
    {
        $anneeId = AnneeScolaire()?->id;
        if (!$anneeId) {
            return collect();
        }

        return CarteModel::where('categorie', self::PROFESSIONNELLE)
            ->where('personne_type', $personneType)
            ->where('annee_scolaire_id', $anneeId)
            ->where('statut', 'active')
            ->where('date_expiration', '>=', now()->toDateString())
            ->whereIn('personne_id', collect($ids)->all())
            ->orderBy('id')->get()->keyBy('personne_id');
    }

    /** Adresse encodée dans le QR code : page de vérification de la carte (réservée aux utilisateurs connectés). */
    public static function urlVerification(CarteModel $carte): string
    {
        return url('/carte/verifier/' . rawurlencode($carte->numero));
    }

    /** Données prêtes à imprimer pour une carte. */
    public function donnees(CarteModel $carte): array
    {
        $ecole = SocieteService::info();
        $scolaire = $carte->categorie === self::SCOLAIRE;

        $nom = $prenom = $matricule = '';
        $details = [];
        $naissance = null;

        if ($carte->personne_type === 'apprenant') {
            $pre = PreInscriptionModel::find($carte->personne_id);
            $inscription = InscriptionModel::with(['classe', 'niveau', 'filiere'])
                ->where('pre_inscription_id', $carte->personne_id)
                ->where('annee_scolaire_id', $carte->annee_scolaire_id)
                ->where('etat', 1)->latest('id')->first();

            $nom = $pre?->nom;
            $prenom = $pre?->prenom;
            $matricule = $inscription?->matricule ?: $pre?->matricule;
            $naissance = $pre?->date_naissance ? Carbon::parse($pre->date_naissance)->format('d/m/Y') : null;
            $details = array_values(array_filter([
                ['Matricule', $matricule],
                ['Classe', $inscription?->filiere?->filiere],
                ['Niveau', $inscription?->niveau?->niveau],
            ], fn ($l) => !empty($l[1])));
        } elseif ($carte->personne_type === 'enseignant') {
            $e = EnseignantModel::find($carte->personne_id);
            $nom = $e?->nom;
            $prenom = $e?->prenom;
            $matricule = $e?->matricule;
            $naissance = $e?->date_naissance ? Carbon::parse($e->date_naissance)->format('d/m/Y') : null;
            $details = array_values(array_filter([
                ['Matricule', $matricule],
                ['Fonction', 'Enseignant'],
                ['Spécialité', $e?->specialite],
            ], fn ($l) => !empty($l[1])));
        } else {
            $e = EmployerModel::find($carte->personne_id);
            $nom = $e?->nom;
            $prenom = $e?->prenom;
            $matricule = $e?->matricule;
            $naissance = $e?->date_naissance ? Carbon::parse($e->date_naissance)->format('d/m/Y') : null;
            $details = array_values(array_filter([
                ['Matricule', $matricule],
                ['Fonction', $e?->poste],
                ['N° CNSS', $e?->numero_cnss],
            ], fn ($l) => !empty($l[1])));
        }

        $photo = PhotoService::chemin($carte->personne_type, $carte->personne_id);

        return [
            'carte'      => $carte,
            'scolaire'   => $scolaire,
            'titre'      => $scolaire ? 'CARTE SCOLAIRE' : 'CARTE PROFESSIONNELLE',
            'couleur'    => $scolaire ? '#1f4e79' : '#1e6b4f',
            'nom'        => mb_strtoupper((string) $nom),
            'prenom'     => (string) $prenom,
            'details'    => $details,
            'naissance'  => $naissance,
            'photo'      => $photo && file_exists($photo) ? $photo : null,
            'personne_type' => $carte->personne_type,
            'personne_id'   => $carte->personne_id,
            'annee'      => $carte->annee_scolaire_id ? \App\Models\Scolarite\AnneeScolaireModel::where('id', $carte->annee_scolaire_id)->value('annee') : null,
            'numero'     => $carte->numero,
            'emission'   => $carte->date_emission->format('d/m/Y'),
            'expiration' => $carte->date_expiration->format('d/m/Y'),
            'barcode'    => Code128::dataUri($carte->numero, 40),
            'qrcode'     => QrCode::dataUri(self::urlVerification($carte)),
            'ecole'      => $ecole,
        ];
    }
}
