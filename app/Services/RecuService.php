<?php

namespace App\Services;

use App\Models\Comptabilite\BanqueModel;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use App\Models\Scolarite\EtatPaieEnseignantModel;
use App\Models\Scolarite\HistoriqueAbonnementModel;
use App\Models\Scolarite\HistoriqueInscriptionModel;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Models\Scolarite\PreInscriptionModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Reçus : pré-inscription, inscription, scolarité, abonnement, salaire, enseignant.
 * Un seul gabarit (A4 ou ticket thermique 80 / 85 mm), toujours imprimé en deux exemplaires.
 */
class RecuService
{
    /** Types de reçu => permissions acceptées (il suffit d'en avoir une). */
    public const TYPES = [
        'preinscription' => ['Scolarite', 'Comptabilite'],
        'inscription'    => ['Scolarite', 'Comptabilite'],
        'scolarite'      => ['Scolarite', 'Comptabilite'],
        'abonnement'     => ['Scolarite', 'Comptabilite'],
        'salaire'        => ['Employer', 'Comptabilite'],
        'enseignant'     => ['Scolarite', 'Comptabilite'],
    ];

    /** Format valide, sinon format par défaut. */
    public static function formatValide(?string $format): string
    {
        $formats = config('documents.recu.formats', []);

        return isset($formats[$format]) ? $format : config('documents.recu.format_defaut', 'a4');
    }

    /* ------------------------------------------------------------------ données */

    /** Données d'un reçu (toutes les valeurs sont déjà mises en forme pour l'impression). */
    public function donnees(string $type, int $id): array
    {
        return match ($type) {
            'preinscription' => $this->preinscription($id),
            'inscription'    => $this->inscription($id),
            'scolarite'      => $this->scolarite($id),
            'abonnement'     => $this->abonnement($id),
            'salaire'        => $this->salaire($id),
            'enseignant'     => $this->enseignant($id),
            default          => abort(404),
        };
    }

    protected function preinscription(int $id): array
    {
        $p = PreInscriptionModel::findOrFail($id);

        return $this->structure('preinscription', 'PRE', "Reçu de pré-inscription", $p, [
            ['Matricule', $p->matricule],
            ['Nom', $p->nom],
            ['Prénom', $p->prenom],
            ['Sexe', $p->sexe],
            ['Date et lieu de naissance', trim($this->date($p->date_naissance) . ' ' . $p->lieu_naissance)],
            ['Téléphone', $p->telephone],
        ], null, "Aucun remboursement n'est possible.", 'Le/La secrétaire', 'Le parent / tuteur');
    }

    protected function inscription(int $id): array
    {
        $h = HistoriqueInscriptionModel::with(['preInscription', 'niveau', 'filiere', 'classe', 'user'])->findOrFail($id);

        $lignes = [
            ['Matricule', $h->matricule],
            ['Nom et prénom', $this->nomComplet($h->preInscription)],
            ['Niveau', $h->niveau?->niveau],
            ['Filière', $h->filiere?->filiere],
            ['Classe affectée', $h->classe?->classe],
            ['Frais d\'inscription', $this->montant($h->frais_inscription)],
        ];
        array_push($lignes, ...$this->lignesPaiement($h));

        return $this->structure('inscription', 'INS', "Reçu d'inscription", $h, $lignes, [
            'libelle' => 'Montant payé', 'valeur' => $h->frais_inscription,
        ], "Aucun remboursement n'est possible.", 'Le/La caissier(ère)', 'Le payeur');
    }

    protected function scolarite(int $id): array
    {
        $h = HistoriquePaiementScolariteModel::with(['inscription.preInscription', 'inscription.niveau', 'inscription.filiere', 'inscription.classe', 'user'])->findOrFail($id);
        $i = $h->inscription;

        $lignes = [
            ['Matricule', $h->matricule],
            ['Nom et prénom', $this->nomComplet($i?->preInscription)],
            ['Niveau', $i?->niveau?->niveau],
            ['Filière', $i?->filiere?->filiere],
            ['Classe', $i?->classe?->classe],
            ['Total frais de scolarité', $this->montant($i?->frais_scolarite ?? 0), 'fort'],
        ];
        if ($h->remise !== null && (float) $h->remise != 0.0) {
            $lignes[] = ['Remise', $this->montant($h->remise)];
        }
        array_push($lignes, ...$this->lignesPaiement($h));

        // Montant restant à payer sur la scolarité après ce paiement
        $lignes[] = ['Total payé', $this->montant($this->totalPayeScolarite($h)), 'fort'];
        $reste = $this->resteAPayerScolarite($h);
        if ($reste !== null) {
            $lignes[] = ['Reste à payer', $this->montant($reste), 'fort'];
        }

        return $this->structure('scolarite', 'SCO', 'Reçu de paiement de scolarité', $h, $lignes, [
            'libelle' => 'Montant payé', 'valeur' => $h->frais_payer,
        ], "Aucun remboursement n'est possible.", 'Le/La caissier(ère)', 'Le payeur');
    }

    protected function abonnement(int $id): array
    {
        $h = HistoriqueAbonnementModel::with(['preInscription', 'type_abonnement', 'user'])->findOrFail($id);
        $remise = (float) ($h->remise ?? 0);

        $lignes = [
            ['Matricule', $h->matricule],
            ['Nom et prénom', $this->nomComplet($h->preInscription)],
            ['Type d\'abonnement', $h->type_abonnement?->type_abonnement],
            ['Période', trim($h->nombre . ' ' . $h->periode)],
            ['Date de début', $this->date($h->date_debut)],
            ['Date de fin', $this->date($h->date_fin)],
            ['Frais d\'abonnement', $this->montant((float) $h->frais_abonnement + $remise)],
        ];
        if ($remise != 0.0) {
            $lignes[] = ['Remise', $this->montant($remise)];
        }
        array_push($lignes, ...$this->lignesPaiement($h));

        return $this->structure('abonnement', 'ABO', "Reçu d'abonnement", $h, $lignes, [
            'libelle' => 'Montant payé', 'valeur' => $h->frais_abonnement,
        ], "Aucun remboursement n'est possible.", 'Le/La caissier(ère)', 'Le payeur');
    }

    protected function salaire(int $id): array
    {
        $h = HistoriquePaiementSalaireModel::with(['employer', 'mois', 'annee', 'user'])->findOrFail($id);

        $lignes = [
            ['Matricule', $h->matricule],
            ['Nom et prénom', $this->nomComplet($h->employer)],
            ['Poste', $h->employer?->poste],
            ['Mois de paie', trim(($h->mois?->mois ?? '') . ' ' . ($h->annee?->annee ?? ''))],
        ];
        array_push($lignes, ...$this->lignesPaiement($h));

        return $this->structure('salaire', 'SAL', 'Reçu de paiement de salaire', $h, $lignes, [
            'libelle' => 'Salaire payé', 'valeur' => $h->salaire_payer,
        ], 'Reçu à conserver.', 'Le comptable', 'Le bénéficiaire');
    }

    protected function enseignant(int $id): array
    {
        $h = HistoriquePaiementEnseignantModel::with(['enseignant', 'mois', 'annee', 'user'])->findOrFail($id);

        $lignes = [
            ['Matricule', $h->matricule],
            ['Nom et prénom', $this->nomComplet($h->enseignant)],
            ['Spécialité', $h->enseignant?->specialite],
            ['Mois de paie', trim(($h->mois?->mois ?? '') . ' ' . ($h->annee?->annee ?? ''))],
        ];
        $r = $this->rubriquesEnseignant($h);
        if ($r['heures'] !== null) {
            $lignes[] = ['Volume horaire total', $r['heures'] . ' H'];
        }
        if ($r['tarif'] > 0) {
            $lignes[] = ['Tarif horaire', $this->montant($r['tarif'])];
        }
        $lignes[] = ['Total brut', $this->montant($r['brut']), 'fort'];
        $lignes[] = ['Retenue / ITS (' . $r['taux'] . ' %)', $this->montant($r['its'])];
        $lignes[] = ['Autres retenues / Avance', $this->montant($r['autres'])];
        array_push($lignes, ...$this->lignesPaiement($h));

        return $this->structure('enseignant', 'ENS', "Reçu de paiement d'enseignant", $h, $lignes, [
            'libelle' => 'Montant payé', 'valeur' => $h->salaire_payer,
        ], 'Reçu à conserver.', 'Le comptable', 'Le bénéficiaire');
    }

    /* ------------------------------------------------------------------ assemblage */

    /** Structure commune : les lignes vides sont retirées, le montant est converti en lettres. */
    protected function structure(string $type, string $prefixe, string $titre, $modele, array $lignes, ?array $montant, string $note, string $signatureGauche, string $signatureDroite): array
    {
        $lignes = array_values(array_filter(
            $lignes,
            fn ($l) => isset($l[1]) && trim((string) $l[1]) !== ''
        ));

        $valeur = $montant['valeur'] ?? null;
        $monnaie = config('documents.monnaie', 'FCFA');

        $emetteur = null;
        if (method_exists($modele, 'user') && $modele->user) {
            $emetteur = trim(($modele->user->prenom ?? '') . ' ' . ($modele->user->nom ?? ''));
        }

        return [
            'type'            => $type,
            'titre'           => $titre,
            'reference'       => $prefixe . '-' . str_pad((string) $modele->id, 6, '0', STR_PAD_LEFT),
            'date'            => $modele->created_at ? Carbon::parse($modele->created_at) : Carbon::now(),
            'lignes'          => $lignes,
            'montant'         => $valeur !== null ? (float) $valeur : null,
            'montant_format'  => $valeur !== null ? number_format((float) $valeur, 0, ',', ' ') . ' ' . $monnaie : null,
            'montant_libelle' => $montant['libelle'] ?? null,
            'lettres'         => $valeur !== null ? 'Arrêté à la somme de : ' . MontantEnLettres::phrase($valeur) : null,
            'note'            => $note,
            'emetteur'        => $emetteur ?: null,
            'signatures'      => [$signatureGauche, $signatureDroite],
        ];
    }

    /**
     * Mode de paiement, nom de la banque quand le paiement n'est pas en espèces
     * (dépôt, transfert, chèque…), puis numéro du compte / chèque / référence s'il existe.
     */
    protected function lignesPaiement($modele): array
    {
        $lignes = [['Mode de paiement', $modele->mode_paiement ?? null]];

        $banque = $this->nomBanque($modele);
        if ($banque !== null) {
            $lignes[] = ['Banque', $banque];
        }

        if (!empty($modele->coordonnee_paiement)) {
            $lignes[] = ['N° ' . ($modele->mode_paiement ?? 'paiement'), $modele->coordonnee_paiement];
        }

        return $lignes;
    }

    /** Vrai si le mode de paiement est « espèces » (ESPECE / ESPÈCES…), ou s'il n'est pas renseigné. */
    protected function estEspeces(?string $mode): bool
    {
        $mode = strtoupper(Str::ascii(trim((string) $mode)));

        return $mode === '' || str_starts_with($mode, 'ESP');
    }

    /** Nom de la banque d'un paiement non espèces ; null pour un paiement en espèces ou sans banque. */
    public function nomBanque($modele): ?string
    {
        if ($this->estEspeces($modele->mode_paiement ?? null) || (int) ($modele->banque_id ?? 0) <= 0) {
            return null;
        }

        $nom = BanqueModel::whereKey((int) $modele->banque_id)->value('banque');

        return $nom !== null && trim((string) $nom) !== '' ? trim((string) $nom) : null;
    }

    /**
     * Reste à payer sur la scolarité APRÈS ce paiement (et non à la date d'impression) :
     * reste actuel de l'inscription + paiements/remises enregistrés après celui-ci.
     * Le même reçu réimprimé plus tard affiche donc toujours le même montant.
     */
    public function totalPayeScolarite(HistoriquePaiementScolariteModel $h): float
    {
        // Cumul des paiements valides de l'inscription, jusqu'à ce paiement inclus.
        return (float) HistoriquePaiementScolariteModel::where('inscription_id', $h->inscription_id)
            ->where('etat', 1)
            ->where('id', '<=', $h->id)
            ->sum('frais_payer');
    }

    /**
     * Rubriques du paiement d'un enseignant, lues dans la fiche de paie du mois :
     * volume horaire, tarif horaire, total brut, et les retenues ITS / autres retenues-avance
     * enregistrées au règlement (optionnelles ; taux affiché : config « its_enseignant », 5 %).
     */
    public function rubriquesEnseignant(HistoriquePaiementEnseignantModel $h): array
    {
        $fiche = EtatPaieEnseignantModel::where('enseignant_id', $h->enseignant_id)
            ->where('mois_id', $h->mois_id)
            ->where(function ($q) use ($h) {
                $q->where('annee_scolaire_id', $h->annee_id);
            })
            ->first();

        $heures = $fiche ? (float) $fiche->heures_sup : null;
        $brut   = (float) ($h->montant_brut ?? 0);
        if ($brut <= 0) {
            $brut = $fiche ? (float) $fiche->total_paye : (float) $h->salaire_payer;
        }
        $taux   = (float) config('documents.its_enseignant', 5);
        // Retenues réellement appliquées au règlement (0 si non cochées / anciens reçus)
        $its    = (float) ($h->retenue_its ?? 0);
        $autres = (float) ($h->autres_retenues ?? 0);

        return [
            'heures' => $heures === null ? null : rtrim(rtrim(number_format($heures, 2, ',', ' '), '0'), ','),
            'tarif'  => (float) ($h->enseignant?->salaire_horaire ?? 0),
            'brut'   => $brut,
            'taux'   => rtrim(rtrim(number_format($taux, 2, '.', ''), '0'), '.'),
            'its'    => $its,
            'autres' => $autres,
        ];
    }

    public function resteAPayerScolarite(HistoriquePaiementScolariteModel $h): ?float
    {
        $inscription = $h->inscription;
        if (!$inscription) {
            return null;
        }

        $ulterieurs = HistoriquePaiementScolariteModel::where('inscription_id', $h->inscription_id)
            ->where('etat', 1)
            ->where('id', '>', $h->id)
            ->selectRaw('COALESCE(SUM(frais_payer), 0) as paye, COALESCE(SUM(remise), 0) as remise')
            ->first();

        $reste = (float) $inscription->rester + (float) ($ulterieurs->paye ?? 0) + (float) ($ulterieurs->remise ?? 0);

        return max(0.0, $reste);
    }

    protected function nomComplet($personne): string
    {
        return $personne ? trim(mb_strtoupper((string) $personne->nom) . ' ' . $personne->prenom) : '';
    }

    protected function montant($valeur): string
    {
        return number_format((float) $valeur, 0, ',', ' ') . ' ' . config('documents.monnaie', 'FCFA');
    }

    protected function date($valeur): string
    {
        if (empty($valeur)) {
            return '';
        }

        try {
            return Carbon::parse($valeur)->format('d/m/Y');
        } catch (\Throwable $e) {
            return (string) $valeur;
        }
    }

    /* ------------------------------------------------------------------ PDF */

    /** PDF du reçu : A4 (2 exemplaires sur la feuille) ou ticket thermique (1 ticket par exemplaire). */
    public function pdf(array $donnees, string $format)
    {
        $format = self::formatValide($format);
        $config = config('documents.recu.formats.' . $format);
        $ecole = SocieteService::info();

        $vue = [
            'd'           => $donnees,
            'ecole'       => $ecole,
            'exemplaires' => config('documents.recu.exemplaires', ['Exemplaire client', 'Exemplaire école']),
            'mode'        => $config['largeur_mm'] ? 'thermique' : 'a4',
        ];

        $pdf = Pdf::loadView('livewire.print.recu.document', $vue);

        if ($config['largeur_mm']) {
            $largeur = $config['largeur_mm'] / 25.4 * 72; // mm -> points
            $pdf->setPaper([0, 0, $largeur, $this->hauteurThermique($donnees, $ecole, $largeur)], 'portrait');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        return $pdf;
    }

    /**
     * Hauteur d'un ticket (en points) estimée d'après le contenu : le papier thermique est
     * continu, inutile de gaspiller du papier avec une hauteur fixe. Marge de sécurité incluse,
     * pour qu'un exemplaire ne déborde jamais sur une 2e page.
     */
    protected function hauteurThermique(array $d, array $ecole, float $largeur): float
    {
        $utile = $largeur - 2 * (3 / 25.4 * 72);                 // marges latérales de 3 mm
        $caracteres = max(20, (int) floor($utile / 3.9));        // police 8 px (6 pt) : ≈ 3,9 pt par caractère (DejaVu Sans, avec marge)
        $lignes = fn (string $texte, float $part = 1.0) => max(1, (int) ceil(mb_strlen($texte) / max(8, $caracteres * $part)));

        $h = 17;                                                  // marges haut / bas (2 × 3 mm)
        $h += 30;                                                 // logo
        $h += 10 * $lignes((string) $ecole['nom'], 0.75);         // raison sociale (plus grande, en gras)
        foreach (['activite', 'adresse', 'contact', 'legal'] as $cle) {
            if (!empty($ecole[$cle])) {
                $h += 7 * $lignes((string) $ecole[$cle]);
            }
        }
        $h += 14;                                                 // 2 traits de séparation
        $h += 10 * $lignes($d['titre'], 0.7) + 18;                // titre, n°, date
        $h += 20;                                                 // bandeau « exemplaire »

        foreach ($d['lignes'] as $ligne) {
            $h += max($lignes((string) $ligne[0], 0.42), $lignes((string) $ligne[1], 0.55)) * 8 + 4;
        }
        if ($d['montant'] !== null) {
            $h += 30 + 2 + 7 * $lignes((string) $d['lettres'], 1.3);
        }

        $h += 26;                                                 // signatures
        $h += 7 * $lignes($d['note']) + 20;                       // note + « imprimé le … »

        // Marge de sécurité : un exemplaire ne doit jamais déborder sur une 2e page.
        return (float) (ceil($h * 1.08) + 10);
    }
}
