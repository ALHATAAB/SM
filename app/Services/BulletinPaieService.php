<?php

namespace App\Services;

use App\Models\Config\SocieteModel;
use App\Models\PaieRH\BulletinCalculerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\RubriqueModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\EtatPaieEnseignantModel;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use Carbon\Carbon;

/**
 * Prépare les bulletins de paie (une structure identique pour les enseignants
 * et le personnel) à partir des états de paie déjà calculés :
 *  - enseignants : etat_paie_enseignant_models (montant de base + heures),
 *  - personnel   : fiche_paie_models + bulletin_calculer_models (rubriques).
 *
 * Le service ne recalcule aucun montant : il présente ce qui a été généré.
 */
class BulletinPaieService
{
    public const CONTRATS = ['fixe' => 'Fixe', 'horaire' => 'Horaire', 'mixte' => 'Mixte'];

    /**
     * Bulletins des enseignants pour un mois de l'année scolaire donnée.
     * Si $etatId est fourni, ne retourne que ce bulletin (404 s'il n'existe pas
     * pour ce mois et cette année).
     *
     * @return array<int, array>
     */
    public function enseignants(int $moisId, int $anneeId, ?int $etatId = null): array
    {
        $etats = EtatPaieEnseignantModel::with('enseignant')
            ->where('mois_id', $moisId)
            ->where('annee_scolaire_id', $anneeId)
            ->when($etatId, fn ($q) => $q->whereKey($etatId))
            ->get()
            ->filter(fn ($e) => $e->enseignant !== null)
            ->sortBy(fn ($e) => mb_strtolower($e->enseignant->nom . ' ' . $e->enseignant->prenom))
            ->values();

        abort_if($etats->isEmpty(), 404, 'Aucun bulletin de paie pour cette sélection.');

        $paiements = HistoriquePaiementEnseignantModel::where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->where('etat', 1)
            ->whereIn('enseignant_id', $etats->pluck('enseignant_id'))
            ->orderBy('id')
            ->get()
            ->keyBy('enseignant_id'); // le dernier paiement actif l'emporte

        $periode = $this->periode($moisId, $anneeId);

        return $etats->map(function ($e) use ($paiements, $periode) {
            $ens    = $e->enseignant;
            $heures = (float) $e->heures_sup; // ce champ stocke les heures effectuées
            $base   = (float) $e->montant_base;
            $mHeure = (float) $e->montant_heures_sup;
            $brut   = $base + $mHeure > 0 ? $base + $mHeure : (float) $e->total_paye;
            $paiement = $paiements->get($e->enseignant_id);
            $taux   = (float) config('documents.its_enseignant', 5);

            // Retenues optionnelles enregistrées au règlement (0 tant que non payé / non appliquées)
            $its    = (float) ($paiement?->retenue_its ?? 0);
            $autres = (float) ($paiement?->autres_retenues ?? 0);
            $net    = max(0.0, $brut - $its - $autres);

            $gains = [];
            if ($base > 0) {
                $gains[] = ['libelle' => 'Salaire de base', 'base' => null, 'taux' => null, 'nombre' => null, 'montant' => $base];
            }
            if ($mHeure > 0) {
                $gains[] = [
                    'libelle' => 'Heures de cours effectuées',
                    'base'    => null,
                    'taux'    => $heures > 0 ? round($mHeure / $heures, 2) : null,
                    'nombre'  => $heures,
                    'montant' => $mHeure,
                ];
            }

            $fonction = trim('Enseignant' . ($ens->specialite ? ' — ' . $ens->specialite : ''));

            return [
                'type'      => 'enseignant',
                'periode'   => $periode,
                'salarie'   => array_values(array_filter([
                    ['Matricule', $ens->matricule],
                    ['Nom et prénom', trim($ens->nom . ' ' . $ens->prenom)],
                    ['Fonction', $fonction],
                    ['Contrat', self::CONTRATS[$ens->type_contrat] ?? ucfirst((string) $ens->type_contrat)],
                    $ens->grade ? ['Grade', $ens->grade] : null,
                    $ens->telephone ? ['Téléphone', $ens->telephone] : null,
                    ['Volume horaire total', $this->nombre($heures) . ' H'],
                    (float) $ens->salaire_horaire > 0 ? ['Tarif horaire', number_format((float) $ens->salaire_horaire, 0, ',', ' ') . ' F'] : null,
                ])),
                'brut'        => $brut,
                'gains'       => $gains,
                'retenues'    => [
                    ['libelle' => 'Retenue / ITS (' . $this->nombre($taux) . ' %)', 'base' => $its > 0 ? $brut : null, 'taux' => $its > 0 ? $taux : null, 'nombre' => null, 'montant' => $its],
                    ['libelle' => 'Autres retenues / Avance', 'base' => null, 'taux' => null, 'nombre' => null, 'montant' => $autres],
                ],
                'total_gain'  => $brut,
                'total_retenu' => $its + $autres,
                'net'         => $net,
                'paiement'    => $this->paiementEnseignant($e->statut, $paiement),
                'signataires' => ['L\'enseignant', 'La Direction'],
            ];
        })->all();
    }

    /**
     * Bulletins du personnel pour un mois de l'année scolaire donnée.
     *
     * @return array<int, array>
     */
    public function personnel(int $moisId, int $anneeId, ?int $ficheId = null): array
    {
        $fiches = FichePaieModel::with('employer')
            ->where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->when($ficheId, fn ($q) => $q->whereKey($ficheId))
            ->get()
            ->filter(fn ($f) => $f->employer !== null)
            ->sortBy(fn ($f) => mb_strtolower($f->employer->nom . ' ' . $f->employer->prenom))
            ->values();

        abort_if($fiches->isEmpty(), 404, 'Aucun bulletin de paie pour cette sélection.');

        $employes = $fiches->pluck('employer_id');

        $lignes = BulletinCalculerModel::with('rubrique')
            ->where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->whereIn('employer_id', $employes)
            ->get()
            ->groupBy('employer_id');

        $paiements = HistoriquePaiementSalaireModel::where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->whereIn('employer_id', $employes)
            ->orderBy('id')
            ->get()
            ->keyBy('employer_id');

        $periode = $this->periode($moisId, $anneeId);

        return $fiches->map(function ($f) use ($lignes, $paiements, $periode) {
            $emp   = $f->employer;
            $rubs  = ($lignes->get($f->employer_id) ?? collect())
                ->sortBy(fn ($l) => $l->rubrique->code_rubrique ?? PHP_INT_MAX);

            $ligne = fn ($l, $montant) => [
                'libelle' => $l->rubrique->rubrique ?? 'Rubrique',
                'auto'    => $l->rubrique->calcul_auto ?? null,
                'base'    => $l->base !== null ? (float) $l->base : null,
                'taux'    => $l->taux !== null ? (float) $l->taux : null,
                'nombre'  => $l->nombre !== null ? (float) $l->nombre : null,
                'montant' => (float) $montant,
            ];

            // Brut = total des gains.
            $brut = (float) $rubs->sum('montant_gain');

            // Répartition du brut soumis en imposable / non imposable (rubriques « Brute I » et « Brute NI »).
            $repartition = $this->repartition($rubs);

            // Base de cotisation = base CNSS utilisée au calcul (brut imposable, plafonné par branche) : on reprend
            // la base de la ligne CNSS du bulletin ; à défaut (CNSS absente du bulletin ou base non renseignée),
            // le brut imposable.
            $ligneCnss = $rubs->first(fn ($l) => ($l->rubrique->calcul_auto ?? null) === 'CNSS' && $l->base !== null);
            $baseCotisation = $ligneCnss ? (float) $ligneCnss->base : (float) $repartition['imposable'];

            // Charges patronales : lignes à part (colonne montant_patronal), hors gains, retenues et net.
            $patronales = $rubs->filter(fn ($l) => (float) $l->montant_patronal != 0)
                ->map(fn ($l) => $ligne($l, $l->montant_patronal))->values()->all();

            // Mois calculé avant l'ajout de la ligne patronale : on affiche au moins le total de la fiche.
            if (empty($patronales) && (float) $f->cnss_patronal > 0) {
                $patronales[] = ['libelle' => 'CNSS (part patronale)', 'base' => null, 'taux' => null, 'nombre' => null, 'montant' => (float) $f->cnss_patronal];
            }
            if (!$rubs->contains(fn ($l) => ($l->rubrique->calcul_auto ?? null) === 'ANPE_PAT') && (float) $f->anpe_patronal > 0) {
                $patronales[] = ['libelle' => 'ANPE (part patronale)', 'base' => null, 'taux' => null, 'nombre' => null, 'montant' => (float) $f->anpe_patronal];
            }

            // Détail des cotisations affiché sur le bulletin : base et retenue CNSS, base ITS, abattement, ITS.
            $auto = fn ($code) => $rubs->first(fn ($l) => ($l->rubrique->calcul_auto ?? null) === $code);
            $lCnss = $auto('CNSS');
            $lIts  = $auto('ITS');
            $details = [
                'base_cnss'       => $lCnss && $lCnss->base !== null ? (float) $lCnss->base : null,
                'taux_cnss'       => $lCnss && $lCnss->taux !== null ? (float) $lCnss->taux : null,
                'cnss'            => $lCnss ? (float) $lCnss->montant_retenu : 0.0,
                'base_its'        => $lIts && $lIts->base !== null ? (float) $lIts->base : null,
                'taux_abattement' => (float) FormuleService::contexteEmploye($emp)['taux_charge'],
                'its'             => $lIts ? (float) $lIts->montant_retenu : 0.0,
            ];
            // Abattement pour frais professionnels : taux actuel des Paramètres paie appliqué à (brut imposable − CNSS)
            $tauxFp = \App\Models\PaieRH\ParametrePaieModel::nombre('its_frais_pro_taux', 0.0);
            $details['taux_frais_pro'] = $tauxFp;
            $details['frais_pro'] = round(max(0.0, (float) $repartition['imposable'] - $details['cnss']) * $tauxFp / 100);

            return [
                'type'    => 'personnel',
                'details' => $details,
                'identite' => $this->identitePersonnel($emp),
                'fiche_id' => $f->id,
                'brut'    => $brut,
                'base_cotisation' => $baseCotisation,
                'brut_imposable'     => $repartition['imposable'],
                'brut_non_imposable' => $repartition['non_imposable'],
                'periode' => $periode,
                'salarie' => array_values(array_filter([
                    ['Matricule', $emp->matricule],
                    ['Nom et prénom', trim($emp->nom . ' ' . $emp->prenom)],
                    ['Poste', $emp->poste],
                    $emp->date_embauche ? ['Date d\'embauche', Carbon::parse($emp->date_embauche)->format('d/m/Y')] : null,
                    $emp->numero_cnss ? ['N° CNSS', $emp->numero_cnss] : null,
                    $emp->charge_familliale !== null && $emp->charge_familliale !== '' ? ['Charge familiale', $emp->charge_familliale] : null,
                    $emp->adresse ? ['Adresse', $emp->adresse] : null,
                    $emp->telephone ? ['Téléphone', $emp->telephone] : null,
                ])),
                'gains'        => $rubs->filter(fn ($l) => (float) $l->montant_gain != 0)->map(fn ($l) => $ligne($l, $l->montant_gain))->values()->all(),
                'retenues'     => $rubs->filter(fn ($l) => (float) $l->montant_retenu != 0)->map(fn ($l) => $ligne($l, $l->montant_retenu))->values()->all(),
                'total_gain'   => (float) $f->total_gain,
                'total_retenu' => (float) $f->total_retenu,
                'patronales'   => $patronales,
                'cnss_patronal' => (float) $f->cnss_patronal,
                'anpe_patronal' => (float) $f->anpe_patronal,
                'net'          => (float) $f->salaire_net,
                'paiement'     => $this->paiementPersonnel($f->statuts, $paiements->get($f->employer_id)),
                'signataires'  => ['L\'employé', 'Le Responsable RH'],
            ];
        })->all();
    }

    /**
     * Bulletin MODÈLE d'un employé (mois « standard » : rubriques permanentes) présenté exactement comme le
     * bulletin calculé et imprimé : mêmes rubriques, mêmes colonnes (base, taux, nombre), brut, base de cotisation,
     * brut imposable / non imposable, retenues CNSS / ITS / FSSP, charges patronales CNSS / ANPE et net à payer.
     * Les montants viennent du même moteur que le calcul de paie : le modèle et le bulletin ne peuvent pas diverger.
     */
    public function modelePersonnel(EmployerModel $emp, ?\Illuminate\Support\Collection $lignes = null): array
    {
        $lignes ??= ModelBulletinModel::with('rubrique')->where('employer_id', $emp->id)->orderBy('id')->get();
        $permanentes = $lignes->filter(fn ($l) => (bool) $l->permanente && $l->rubrique !== null)->values();
        $calcul = app(CalculPaiePersonnelService::class)->calculer($emp, $permanentes);

        $estAuto = fn ($l) => !empty($l->rubrique->calcul_auto);
        $manuelles = $permanentes->reject($estAuto);

        // Rubriques générées par le calcul (CNSS, ITS, FSSP) et parts patronales : même source que le bulletin calculé
        $autres = collect($calcul['lignes'] ?? []);
        if (!empty($calcul['patronale'])) {
            $autres->push($calcul['patronale']);
        }
        if (!empty($calcul['patronale_anpe'])) {
            $autres->push($calcul['patronale_anpe']);
        }
        $rubriquesAuto = RubriqueModel::whereIn('id', $autres->pluck('rubrique_id'))->get()->keyBy('id');

        $ligne = fn ($libelle, $base, $taux, $nombre, $montant, $code, $auto = null) => [
            'libelle' => $libelle,
            'auto'    => $auto,
            'base'    => $base !== null ? (float) $base : null,
            'taux'    => $taux !== null ? (float) $taux : null,
            'nombre'  => $nombre !== null ? (float) $nombre : null,
            'montant' => (float) $montant,
            'code'    => $code ?? PHP_INT_MAX,
        ];
        $deLigne = fn ($l, $montant) => $ligne($l->rubrique->rubrique ?? 'Rubrique', $l->base, $l->taux, $l->nombre, $montant, $l->rubrique->code_rubrique ?? null, $l->rubrique->calcul_auto ?? null);
        $deCalcul = function ($a, $montant) use ($ligne, $rubriquesAuto) {
            $r = $rubriquesAuto->get($a['rubrique_id']);

            return $ligne($r->rubrique ?? 'Rubrique', $a['base'] ?? null, $a['taux'] ?? null, null, $montant, $r->code_rubrique ?? null, $r->calcul_auto ?? null);
        };
        $trier = fn ($c) => collect($c)->sortBy('code')->map(fn ($x) => collect($x)->except('code')->all())->values()->all();

        $gains = $manuelles->filter(fn ($l) => (float) $l->montant_gain != 0)->map(fn ($l) => $deLigne($l, $l->montant_gain));
        $retenues = $manuelles->filter(fn ($l) => (float) $l->montant_retenu != 0)->map(fn ($l) => $deLigne($l, $l->montant_retenu))
            ->concat(collect($calcul['lignes'] ?? [])->map(fn ($a) => $deCalcul($a, $a['montant_retenu'])));

        $patronales = $manuelles->filter(fn ($l) => (float) $l->montant_patronal != 0)->map(fn ($l) => $deLigne($l, $l->montant_patronal));
        foreach (['patronale', 'patronale_anpe'] as $cle) {
            if (!empty($calcul[$cle])) {
                $patronales->push($deCalcul($calcul[$cle], $calcul[$cle]['montant_patronal']));
            }
        }

        $baseCotisation = (float) ($calcul['cnss']['base'] ?? 0) > 0 ? (float) $calcul['cnss']['base'] : (float) $calcul['brut_imposable'];

        $details = [
            'base_cnss'       => $calcul['cnss']['base'] ?? null,
            'taux_cnss'       => $calcul['cnss']['taux'] ?? null,
            'cnss'            => (float) ($calcul['cnss']['salarie'] ?? 0),
            'base_its'        => (float) ($calcul['its']['impot'] ?? 0) > 0 ? (float) $calcul['its']['base_its'] : null,
            'taux_frais_pro'  => (float) ($calcul['its']['taux_frais_pro'] ?? 0),
            'frais_pro'       => (float) ($calcul['its']['frais_pro'] ?? 0),
            'taux_abattement' => (float) ($calcul['its']['taux_abattement'] ?? 0),
            'its'             => (float) ($calcul['its']['impot'] ?? 0),
        ];

        return [
            'type'    => 'personnel',
            'details' => $details,
            'identite' => $this->identitePersonnel($emp),
            'modele'  => true,
            'brut'    => (float) $calcul['gain'],
            'base_cotisation'    => $baseCotisation,
            'brut_imposable'     => (float) $calcul['brut_imposable'],
            'brut_non_imposable' => (float) $calcul['brut_non_imposable'],
            'periode' => 'Bulletin modèle (rubriques permanentes)',
            'salarie' => $this->infosPersonnel($emp),
            'gains'        => $trier($gains),
            'retenues'     => $trier($retenues),
            'total_gain'   => (float) $calcul['gain'],
            'total_retenu' => (float) $calcul['retenu'],
            'patronales'   => $trier($patronales),
            'cnss_patronal' => (float) $calcul['patronal'],
            'anpe_patronal' => (float) $calcul['anpe_patronal'],
            'net'          => (float) $calcul['net'],
            'paiement'     => null,
            'signataires'  => ['L\'employé', 'Le Responsable RH'],
        ];
    }

    /** Identité de l'employé pour le bloc « salarié » du bulletin au format de l'établissement. */
    protected function identitePersonnel($emp): array
    {
        return [
            'matricule'     => $emp->matricule,
            'nom'           => trim((string) $emp->nom),
            'prenom'        => trim((string) $emp->prenom),
            'fonction'      => $emp->poste,
            'numero_cnss'   => $emp->numero_cnss,
            'date_embauche' => $emp->date_embauche ? Carbon::parse($emp->date_embauche)->format('d/m/Y') : null,
            'charge'        => $emp->charge_familliale !== null && $emp->charge_familliale !== '' ? $emp->charge_familliale : 0,
            'anciennete'    => FormuleService::anciennete($emp->date_embauche),
        ];
    }

    /** Informations de l'employé affichées en tête du bulletin (identiques sur le bulletin modèle et calculé). */
    protected function infosPersonnel($emp): array
    {
        return array_values(array_filter([
            ['Matricule', $emp->matricule],
            ['Nom et prénom', trim($emp->nom . ' ' . $emp->prenom)],
            ['Poste', $emp->poste],
            $emp->date_embauche ? ['Date d\'embauche', Carbon::parse($emp->date_embauche)->format('d/m/Y')] : null,
            $emp->numero_cnss ? ['N° CNSS', $emp->numero_cnss] : null,
            $emp->charge_familliale !== null && $emp->charge_familliale !== '' ? ['Charge familiale', $emp->charge_familliale] : null,
            $emp->adresse ? ['Adresse', $emp->adresse] : null,
            $emp->telephone ? ['Téléphone', $emp->telephone] : null,
        ]));
    }

    /**
     * État de paie du personnel pour un mois : une ligne par employé, une colonne par rubrique
     * de gain puis par rubrique de retenue réellement présente sur les bulletins du mois.
     *
     * @return array{
     *   periode: string, fiches: \Illuminate\Support\Collection,
     *   gains: array<int,string>, retenues: array<int,string>,
     *   montants: array<int, array{g: array<int,float>, r: array<int,float>}>,
     *   totaux: array{g: array<int,float>, r: array<int,float>, gain: float, retenu: float, patronal: float, anpe: float, net: float}
     * }
     */
    public function etatPersonnel(int $moisId, int $anneeId): array
    {
        $fiches = FichePaieModel::with('employer')
            ->where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->get()
            ->filter(fn ($f) => $f->employer !== null)
            ->sortBy(fn ($f) => mb_strtolower($f->employer->nom . ' ' . $f->employer->prenom))
            ->values();

        $lignes = BulletinCalculerModel::with('rubrique')
            ->where('mois_id', $moisId)
            ->where('annee_id', $anneeId)
            ->whereIn('employer_id', $fiches->pluck('employer_id'))
            ->get()
            ->filter(fn ($l) => $l->rubrique !== null);

        $codes    = [];
        $gains    = [];
        $retenues = [];
        $montants = [];
        $totaux   = ['g' => [], 'r' => [], 'gain' => 0.0, 'retenu' => 0.0, 'patronal' => 0.0, 'anpe' => 0.0, 'net' => 0.0, 'imposable' => 0.0, 'non_imposable' => 0.0];
        $repartitions = [];
        foreach ($lignes->groupBy('employer_id') as $employerId => $rubsEmploye) {
            $repartitions[$employerId] = $this->repartition($rubsEmploye);
            $totaux['imposable']     += $repartitions[$employerId]['imposable'];
            $totaux['non_imposable'] += $repartitions[$employerId]['non_imposable'];
        }

        foreach ($lignes as $l) {
            $rid = $l->rubrique_id;
            $codes[$rid] = $l->rubrique->code_rubrique;

            if ((float) $l->montant_gain != 0) {
                $gains[$rid] = $l->rubrique->rubrique;
                $montants[$l->employer_id]['g'][$rid] = ($montants[$l->employer_id]['g'][$rid] ?? 0) + (float) $l->montant_gain;
                $totaux['g'][$rid] = ($totaux['g'][$rid] ?? 0) + (float) $l->montant_gain;
            }

            if ((float) $l->montant_retenu != 0) {
                $retenues[$rid] = $l->rubrique->rubrique;
                $montants[$l->employer_id]['r'][$rid] = ($montants[$l->employer_id]['r'][$rid] ?? 0) + (float) $l->montant_retenu;
                $totaux['r'][$rid] = ($totaux['r'][$rid] ?? 0) + (float) $l->montant_retenu;
            }
        }

        $trier = function (array $colonnes) use ($codes) {
            uksort($colonnes, fn ($a, $b) => ($codes[$a] ?? PHP_INT_MAX) <=> ($codes[$b] ?? PHP_INT_MAX));

            return $colonnes;
        };

        foreach ($fiches as $f) {
            $totaux['gain']   += (float) $f->total_gain;
            $totaux['retenu'] += (float) $f->total_retenu;
            $totaux['patronal'] += (float) $f->cnss_patronal;
            $totaux['anpe']     += (float) $f->anpe_patronal;
            $totaux['net']    += (float) $f->salaire_net;
        }

        return [
            'periode'  => $this->periode($moisId, $anneeId),
            'fiches'   => $fiches,
            'gains'    => $trier($gains),
            'retenues' => $trier($retenues),
            'montants' => $montants,
            'repartitions' => $repartitions,
            'totaux'   => $totaux,
            'ecole'    => $this->ecole(),
        ];
    }

    /**
     * Répartition du brut soumis (rubriques « Brute I » et « Brute NI ») en brut imposable et non imposable.
     * Part imposable enregistrée sur la ligne ; à défaut (anciens bulletins) : « Brute I » imposable, « Brute NI » non imposable.
     *
     * @return array{imposable: float, non_imposable: float}
     */
    public function repartition($lignes): array
    {
        $imposable = $non = 0.0;

        foreach ($lignes as $l) {
            $type = $l->rubrique->type_rubrique ?? null;
            $gain = (float) $l->montant_gain;
            if ($gain == 0.0 || !in_array($type, ['Brute I', 'Brute NI'], true)) {
                continue;
            }

            $part = CalculPaiePersonnelService::partsLigne($l, $gain)[0]; // part soumise à l'ITS (brut imposable)
            $imposable += $part;
            $non       += $gain - $part;
        }

        return ['imposable' => $imposable, 'non_imposable' => $non];
    }

    /**
     * Informations de l'établissement (paramètre « Société »), avec les valeurs
     * par défaut utilisées par les autres documents PDF.
     */
    public function ecole(): array
    {
        $i = \App\Services\SocieteService::info();

        return [
            'nom'     => $i['nom'],
            'adresse' => $i['adresse'],
            'contact' => $i['contact'],
            'logo'    => $i['logo'],
        ];
    }

    protected function periode(int $moisId, int $anneeId): string
    {
        $mois  = MoisPaieModel::where('id', $moisId)->value('mois');
        $annee = AnneeScolaireModel::where('id', $anneeId)->value('annee');

        return trim(($mois ?? '') . ' ' . ($annee ?? ''));
    }

    protected function paiementEnseignant(?string $statut, $historique): array
    {
        $paye = $historique !== null || $statut === 'payé';

        return [
            'paye'    => $paye,
            'statut'  => $paye ? 'Payé' : 'En attente de paiement',
            'date'    => $historique?->date_comptable ? Carbon::parse($historique->date_comptable)->format('d/m/Y') : null,
            'mode'    => $historique?->mode_paiement,
        ];
    }

    protected function paiementPersonnel(?string $statut, $historique): array
    {
        $paye = $historique !== null || strtoupper((string) $statut) === 'PAIE';

        return [
            'paye'    => $paye,
            'statut'  => $paye ? 'Payé' : 'En attente de paiement',
            'date'    => $historique?->date_comptable ? Carbon::parse($historique->date_comptable)->format('d/m/Y') : null,
            'mode'    => $historique?->mode_paiement,
        ];
    }

    protected function nombre(float $v): string
    {
        return rtrim(rtrim(number_format($v, 2, ',', ''), '0'), ',') ?: '0';
    }
}
