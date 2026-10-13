<?php

namespace App\Services;

use App\Models\PaieRH\CotisationModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\MontantTrancheFiscaleModel;
use App\Models\PaieRH\ParametrePaieModel;
use App\Models\PaieRH\RubriqueModel;
use App\Models\PaieRH\TauxChargeFamillialeModel;
use App\Models\PaieRH\TauxTrancheFiscaleModel;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Calcul automatique de la CNSS (part salariale), de l'ITS, de la CNSS patronale et de l'ANPE patronale
 * (charges employeur, hors net) pour le PERSONNEL.
 * Le bulletin enseignant n'utilise pas ce service.
 *
 * Ordre du calcul :
 *   1. Assiette CNSS / ANPE = brut imposable (gains « Brute I », part imposable des gains partagés), plafonnée par branche.
 *                           Les gains « Brute NI » et la part non imposable n'entrent pas dans cette base.
 *   2. CNSS salarié       = somme des branches actives (assiette plafonnée × taux salarié).
 *   3. Base imposable     = gains « Brute I » − CNSS salarié.
 *   4. Abattement charges = base imposable × taux selon le nombre de personnes à charge.
 *   5. Revenu net imposable = (base − abattement), arrondi au millier inférieur.
 *   6. ITS                = barème par tranches (taux marginaux ; à défaut, valeur fixe de la tranche).
 *   7. FSSP               = 1 % (config/paie.php) du salaire net avant FSSP
 *                           (gains − retenues saisies − CNSS − ITS), retenu en dernier.
 *
 * Tous les taux, plafonds et tranches viennent des écrans Paramètre paie RH : rien n'est figé ici.
 *
 * Options par rubrique et par employé (bulletin modèle) :
 *   - Mode de calcul « manuel » (CNSS, ITS, FSSP) : le montant saisi remplace le montant calculé ; les rubriques
 *     calculées ensuite en tiennent compte (ITS sur la CNSS saisie, FSSP sur le net après CNSS et ITS saisis).
 *     Les parts patronales (CNSS, ANPE) restent calculées d'après les barèmes.
 *   - Répartition « imposable / non imposable » d'un gain : montant_imposable (le reste est non imposable) ;
 *     à défaut, tout le gain est imposable pour une rubrique « Brute I » et non imposable pour « Brute NI ».
 *   - Périodicité : rubrique permanente (chaque mois) ou « ce mois seulement » (mois_id / annee_id).
 *
 * Une rubrique automatique (CNSS, ITS, FSSP) ne s'applique à un employé que si elle figure sur son
 * bulletin modèle. Son montant est alors recalculé (synchroniserModele) à chaque changement :
 * ajout/retrait d'une rubrique, modification des personnes à charge, ouverture de la fiche.
 */
class CalculPaiePersonnelService
{
    protected ?array $parametres = null;

    /**
     * @param  Collection  $lignes  ModelBulletinModel (avec rubrique) d'un employé
     * @param  float  $prorata  part du mois travaillée (1 = mois complet)
     * @param  ?int  $moisId  mois calculé : les rubriques « ce mois seulement » de ce mois sont prises en compte
     *                        (sans mois, seules les rubriques permanentes comptent : aperçu du bulletin modèle)
     * @param  array<string,int>  $ajouts  rubriques automatiques à considérer comme présentes
     *                                     en plus des lignes : ['CNSS' => rubrique_id] (aperçu avant ajout)
     * @return array{gain: float, retenu: float, net: float, brut_imposable: float, brut_non_imposable: float, fssp: float, cnss: array, its: array, lignes: array, patronale: ?array, patronale_anpe: ?array}
     */
    public function calculer(EmployerModel $employe, Collection $lignes, array $ajouts = [], float $prorata = 1.0, ?int $moisId = null, ?int $anneeId = null): array
    {
        // Rubriques du mois : les permanentes, plus celles « ce mois seulement » du mois calculé.
        $lignes = $this->lignesDuMois($lignes, $moisId, $anneeId);

        $manuelles = $lignes->filter(fn ($l) => !$this->estAuto($l));

        // Rubriques automatiques présentes sur le bulletin de l'employé : code => rubrique_id
        $actives = $lignes->filter(fn ($l) => $this->estAuto($l))
            ->mapWithKeys(fn ($l) => [$l->rubrique->calcul_auto => $l->rubrique_id])
            ->all() + $ajouts;

        // Rubriques automatiques dont le montant est saisi à la main : code => ligne du bulletin modèle
        $saisies = $lignes->filter(fn ($l) => $this->estAuto($l) && $this->estManuel($l))
            ->mapWithKeys(fn ($l) => [$l->rubrique->calcul_auto => $l])
            ->all();
        $forces = array_map(fn ($l) => (float) $l->montant_retenu, $saisies);

        // Prorata (départ en cours de mois) : seuls les GAINS sont proratisés ; les retenues saisies
        // (avances, prêts...) restent entières. CNSS, ITS et FSSP se calculent sur les gains proratisés.
        $gainLigne = fn ($l) => $prorata < 1 ? round((float) $l->montant_gain * $prorata) : (float) $l->montant_gain;

        $gain   = (float) $manuelles->sum($gainLigne);
        $retenu = (float) $manuelles->sum('montant_retenu');

        // Répartition du brut soumis (rubriques « Brute I » et « Brute NI ») en imposable / non imposable.
        // $brutI = base ITS (brut imposable) ; $brutCnss = base CNSS : une prime peut être exclue de l'une sans l'autre.
        $brutI = $brutNI = $brutCnss = 0.0;
        foreach ($manuelles as $l) {
            if (!in_array($l->rubrique->type_rubrique ?? null, ['Brute I', 'Brute NI'], true)) {
                continue;
            }
            $montant = $gainLigne($l);
            [$imposable, $cnssPart] = self::partsLigne($l, $montant);
            $brutI    += $imposable;
            $brutNI   += $montant - $imposable;
            $brutCnss += $cnssPart;
        }

        $p        = $this->parametres();
        $tauxFssp = ParametrePaieModel::nombre('fssp_taux', (float) config('paie.fssp_taux', 1)); // modifiable dans Paramètres paie

        // Un barème n'est appliqué que si sa rubrique est sur le bulletin.
        $res = self::calculerMontants(
            $brutI,
            $brutCnss, // base CNSS salariale : gains soumis à la CNSS (par défaut : brut imposable)
            (int) $employe->charge_familliale,
            isset($actives['CNSS']) ? $p['cotisations'] : [],
            $p['abattements'],
            isset($actives['ITS']) ? $p['tranches_taux'] : [],
            isset($actives['ITS']) ? $p['tranches_montant'] : [],
            $tauxFssp,
            array_intersect_key($forces, ['CNSS' => 1, 'ITS' => 1]),
            ParametrePaieModel::nombre('its_frais_pro_taux', 0.0),
            // Charges patronales : sur le total des gains (plafonné) ou sur la même base que la CNSS salariale
            ParametrePaieModel::texte('cnss_patronal_base', 'cnss') === 'brut' ? $gain : null
        );

        $sorties = [];

        if (isset($actives['CNSS']) && $res['cnss']['salarie'] > 0) {
            $m = isset($saisies['CNSS']);
            $sorties[] = [
                'rubrique_id'    => $actives['CNSS'],
                'base'           => $m ? $this->nullableFloat($saisies['CNSS']->base) : $res['cnss']['base'],
                'taux'           => $m ? $this->nullableFloat($saisies['CNSS']->taux) : $res['cnss']['taux'],
                'montant_retenu' => $res['cnss']['salarie'],
                'manuel'         => $m,
            ];
            $retenu += $res['cnss']['salarie'];
        }

        if (isset($actives['ITS']) && $res['its']['impot'] > 0) {
            $m = isset($saisies['ITS']);
            $sorties[] = [
                'rubrique_id'    => $actives['ITS'],
                'base'           => $m ? $this->nullableFloat($saisies['ITS']->base) : $res['its']['base_its'],
                'taux'           => $m ? $this->nullableFloat($saisies['ITS']->taux) : null,
                'montant_retenu' => $res['its']['impot'],
                'manuel'         => $m,
            ];
            $retenu += $res['its']['impot'];
        }

        // FSSP : pourcentage du net calculé après CNSS et ITS (ou montant saisi à la main)
        $netAvantFssp = $gain - $retenu;
        $fsspManuel   = isset($saisies['FSSP']);
        $fssp = isset($actives['FSSP'])
            ? ($fsspManuel ? max(0.0, (float) $forces['FSSP']) : self::fssp($netAvantFssp, $tauxFssp))
            : 0.0;
        if ($fssp > 0) {
            $sorties[] = [
                'rubrique_id'    => $actives['FSSP'],
                'base'           => $fsspManuel ? $this->nullableFloat($saisies['FSSP']->base) : $netAvantFssp,
                'taux'           => $fsspManuel ? $this->nullableFloat($saisies['FSSP']->taux) : $tauxFssp,
                'montant_retenu' => $fssp,
                'manuel'         => $fsspManuel,
            ];
            $retenu += $fssp;
        }

        // CNSS part patronale : ligne du bulletin à part, jamais ajoutée à $gain ni à $retenu (le net reste inchangé).
        $patronale = null;
        $rubriquePat = RubriqueModel::where('calcul_auto', 'CNSS_PAT')->value('id');
        if (isset($actives['CNSS']) && $res['cnss']['patronal'] > 0 && $rubriquePat) {
            $patronale = [
                'rubrique_id'      => $rubriquePat,
                'base'             => $res['cnss']['base_patronal'],
                'taux'             => $res['cnss']['taux_patronal'],
                'montant_patronal' => $res['cnss']['patronal'],
            ];
        }

        // ANPE part patronale : même principe (ligne à part, hors gains/retenues/net), générée avec la CNSS.
        $patronaleAnpe = null;
        $rubriqueAnpe = RubriqueModel::where('calcul_auto', 'ANPE_PAT')->value('id');
        if (isset($actives['CNSS']) && $res['anpe']['patronal'] > 0 && $rubriqueAnpe) {
            $patronaleAnpe = [
                'rubrique_id'      => $rubriqueAnpe,
                'base'             => $res['anpe']['base'],
                'taux'             => $res['anpe']['taux'],
                'montant_patronal' => $res['anpe']['patronal'],
            ];
        }

        return [
            'gain'   => $gain,
            'retenu' => $retenu,
            'net'    => $gain - $retenu,
            'brut_imposable'     => $brutI,
            'brut_non_imposable' => $brutNI,
            'fssp'   => $fssp,
            'patronale' => $patronale,
            'patronale_anpe' => $patronaleAnpe,
            'patronal' => isset($actives['CNSS']) ? (float) $res['cnss']['patronal'] : 0.0, // charge employeur, hors net
            'anpe_patronal' => isset($actives['CNSS']) ? (float) $res['anpe']['patronal'] : 0.0, // charge employeur, hors net
            'anpe'   => $res['anpe'],
            'cnss'   => $res['cnss'],
            'its'    => $res['its'],
            'lignes' => $sorties,
        ];
    }

    /**
     * Recalcule et enregistre le montant des rubriques automatiques (CNSS, ITS, FSSP)
     * présentes sur le bulletin modèle de l'employé, d'après ses informations
     * (personnes à charge) et ses rubriques de gain.
     */
    public function synchroniserModele(EmployerModel $employe): void
    {
        $lignes = ModelBulletinModel::with('rubrique')->where('employer_id', $employe->id)->get();

        // Rubriques à formule : montants remis à jour (constantes modifiées) avant le calcul CNSS / ITS / FSSP.
        $this->recalculerFormules($lignes);

        if (!$lignes->contains(fn ($l) => $this->estAuto($l))) {
            return;
        }

        // Le bulletin modèle montre un mois « standard » : rubriques permanentes seulement.
        $sorties = collect($this->calculer($employe, $lignes)['lignes'])->keyBy('rubrique_id');

        foreach ($lignes->filter(fn ($l) => $this->estAuto($l)) as $ligne) {
            // Montant saisi à la main : jamais écrasé par le calcul.
            if ($this->estManuel($ligne)) {
                continue;
            }

            $s = $sorties->get($ligne->rubrique_id);
            $ligne->update([
                'base'           => $s['base'] ?? null,
                'taux'           => $s['taux'] ?? null,
                'montant_gain'   => null,
                'montant_retenu' => $s['montant_retenu'] ?? 0,
            ]);
        }
    }

    /**
     * Recalcule et enregistre le montant des rubriques « automatiques » à formule (base, taux, nombre + constantes).
     * Une ligne en mode « manuel » (montant saisi) n'est jamais touchée. Une formule devenue invalide
     * (constante supprimée...) laisse le dernier montant inchangé.
     */
    public function recalculerFormules(Collection $lignes): void
    {
        $formules = app(FormuleService::class);

        foreach ($lignes as $ligne) {
            $rubrique = $ligne->rubrique;
            if (!$rubrique || !$rubrique->estFormule() || ($ligne->mode_calcul ?? 'auto') === 'manuel') {
                continue;
            }

            try {
                $montant = max(0.0, round($formules->evaluer($rubrique->formule, [
                    'base'   => $ligne->base,
                    'taux'   => $ligne->taux,
                    'nombre' => $ligne->nombre,
                    ...FormuleService::contexteEmploye($ligne->employer), // ancienneté, charges de famille, abattement
                ])));
            } catch (InvalidArgumentException) {
                continue;
            }

            $champs = match ($rubrique->sens_rubrique) {
                'Gain'     => ['montant_gain' => $montant],
                'Patronal' => ['montant_patronal' => $montant],
                default    => ['montant_retenu' => $montant],
            };

            // Part imposable partagée : proportion conservée si le gain change
            if ($rubrique->sens_rubrique === 'Gain' && $ligne->montant_imposable !== null && (float) $ligne->montant_gain > 0) {
                $champs['montant_imposable'] = min($montant, round($montant * (float) $ligne->montant_imposable / (float) $ligne->montant_gain));
            }

            $ligne->update($champs);
        }
    }

    /** Montant qu'aurait la rubrique automatique si on l'ajoutait maintenant au bulletin de l'employé. */
    public function apercu(EmployerModel $employe, RubriqueModel $rubrique): float
    {
        if (!$rubrique->estAuto()) {
            return 0.0;
        }

        $lignes = ModelBulletinModel::with('rubrique')->where('employer_id', $employe->id)->get();
        $calcul = $this->calculer($employe, $lignes, [$rubrique->calcul_auto => $rubrique->id]);

        $ligne = collect($calcul['lignes'])->firstWhere('rubrique_id', $rubrique->id);

        return (float) ($ligne['montant_retenu'] ?? 0);
    }

    /**
     * Cœur du calcul, sans accès base de données (testable seul).
     *
     * @param  array $cotisations   [['taux_salarie'=>, 'taux_patronal'=>, 'plafond_mensuel'=>, 'organisme'=>'CNSS|ANPE'], ...] (actives)
     * @param  array $abattements   [nombre_de_charge => taux %]
     * @param  array $tranchesTaux  [['min'=>, 'max'=>, 'taux'=>%], ...]
     * @param  array $tranchesMontant [['min'=>, 'max'=>, 'valeur'=>], ...]
     * @param  array $forces        montants saisis à la main qui remplacent le calcul : ['CNSS' => montant, 'ITS' => montant]
     */
    public static function calculerMontants(
        float $brutImposable,
        float $brutSoumisCnss,
        int $charges,
        array $cotisations,
        array $abattements,
        array $tranchesTaux,
        array $tranchesMontant = [],
        float $tauxFssp = 0,
        array $forces = [],
        float $tauxFraisPro = 0.0,
        ?float $brutPatronal = null
    ): array {
        // --- CNSS
        $salarie = 0.0;
        $patronal = 0.0;
        $anpe = 0.0;
        $baseMax = 0.0;
        $baseMaxPat = 0.0;
        $baseMaxAnpe = 0.0;
        foreach ($cotisations as $c) {
            $plafond = (float) ($c['plafond_mensuel'] ?? 0);
            $base    = $plafond > 0 ? min($brutSoumisCnss, $plafond) : $brutSoumisCnss;
            $assietteBrute = $brutPatronal ?? $brutSoumisCnss; // base des charges patronales
            $basePat = $plafond > 0 ? min($assietteBrute, $plafond) : $assietteBrute;
            $salarie  += $base * (float) $c['taux_salarie'] / 100;
            if ((float) $c['taux_salarie'] > 0) {
                $baseMax = max($baseMax, $base);
            }

            // Part patronale : les branches ANPE forment une ligne à part de la CNSS patronale.
            if (strtoupper((string) ($c['organisme'] ?? 'CNSS')) === 'ANPE') {
                $anpe += $basePat * (float) $c['taux_patronal'] / 100;
                if ((float) $c['taux_patronal'] > 0) {
                    $baseMaxAnpe = max($baseMaxAnpe, $basePat);
                }
            } else {
                $patronal += $basePat * (float) $c['taux_patronal'] / 100;
                if ((float) $c['taux_patronal'] > 0) {
                    $baseMaxPat = max($baseMaxPat, $basePat);
                }
            }
        }
        $salarie  = isset($forces['CNSS']) ? max(0.0, round((float) $forces['CNSS'])) : round($salarie);
        $patronal = round($patronal);
        $anpe     = round($anpe);
        $tauxCnss = $baseMax > 0 ? round($salarie / $baseMax * 100, 2) : null;
        // Taux patronal CNSS (somme des branches hors ANPE) rapporté à la base la plus haute : 16,4 % si toutes les branches ont le même plafond
        $tauxPat  = $baseMaxPat > 0 ? round($patronal / $baseMaxPat * 100, 2) : null;
        $tauxAnpe = $baseMaxAnpe > 0 ? round($anpe / $baseMaxAnpe * 100, 2) : null;

        // --- Base imposable, frais professionnels, base des retenues ITS, abattement charges de famille
        $base = max(0.0, $brutImposable - $salarie);

        // Abattement pour frais professionnels (non arrondi, comme la feuille de calcul de l'établissement)
        $fraisPro = $base * max(0.0, $tauxFraisPro) / 100;
        // « Base retenues ITS » : arrondie au millier inférieur
        $baseIts = floor(max(0.0, $base - $fraisPro) / 1000) * 1000;

        $tauxAbat = 0.0;
        $trouve = null;
        foreach ($abattements as $nombre => $taux) {
            if ((int) $nombre <= $charges && ($trouve === null || (int) $nombre > $trouve)) {
                $trouve = (int) $nombre;
                $tauxAbat = (float) $taux;
            }
        }
        // Revenu net imposable : base ITS après abattement pour charges de famille
        $rni        = $baseIts * (1 - $tauxAbat / 100);
        $abattement = round($baseIts - $rni);

        // --- ITS
        $impot = isset($forces['ITS'])
            ? max(0.0, round((float) $forces['ITS']))
            : self::impot($rni, $tranchesTaux, $tranchesMontant);

        // Net avant FSSP = brut imposable − CNSS − ITS (cas sans autre rubrique)
        $fssp = self::fssp($brutImposable - $salarie - $impot, $tauxFssp);

        return [
            'fssp' => $fssp,
            'cnss' => [
                'base'     => $baseMax > 0 ? $baseMax : null,
                'taux'     => $tauxCnss,
                'salarie'  => $salarie,
                'patronal' => $patronal,
                'base_patronal' => $baseMaxPat > 0 ? $baseMaxPat : null,
                'taux_patronal' => $tauxPat,
            ],
            'anpe' => [
                'base'     => $baseMaxAnpe > 0 ? $baseMaxAnpe : null,
                'taux'     => $tauxAnpe,
                'patronal' => $anpe,
            ],
            'its' => [
                'base_imposable' => $base,
                'taux_frais_pro' => $tauxFraisPro,
                'frais_pro'      => round($fraisPro),
                'base_its'       => $baseIts,
                'taux_abattement' => $tauxAbat,
                'abattement'     => $abattement,
                'rni'            => $rni,
                'impot'          => $impot,
            ],
        ];
    }

    /** FSSP = taux % du net (avant FSSP), arrondi au franc. */
    public static function fssp(float $netAvantFssp, float $taux): float
    {
        return $netAvantFssp > 0 && $taux > 0 ? round($netAvantFssp * $taux / 100) : 0.0;
    }

    /** Barème par tranches (taux marginaux) ; à défaut, valeur fixe de la tranche contenant le revenu. */
    public static function impot(float $rni, array $tranchesTaux, array $tranchesMontant = []): float
    {
        if ($rni <= 0) {
            return 0.0;
        }

        if (!empty($tranchesTaux)) {
            usort($tranchesTaux, fn ($a, $b) => $a['min'] <=> $b['min']);
            $total = 0.0;
            foreach ($tranchesTaux as $t) {
                $min = (float) $t['min'];
                $max = (float) $t['max'];
                $haut = $max > $min ? min($rni, $max) : $rni; // max <= min : dernière tranche, sans limite
                $part = $haut - $min;
                if ($part > 0) {
                    $total += $part * (float) $t['taux'] / 100;
                }
            }
            return round($total);
        }

        foreach ($tranchesMontant as $t) {
            $min = (float) $t['min'];
            $max = (float) $t['max'];
            if ($rni >= $min && ($max <= $min || $rni <= $max)) {
                return round((float) $t['valeur']);
            }
        }

        return 0.0;
    }

    protected function estAuto($ligne): bool
    {
        return !empty($ligne->rubrique?->calcul_auto);
    }

    /** Rubrique automatique dont le montant est saisi à la main sur le bulletin de l'employé. */
    protected function estManuel($ligne): bool
    {
        return ($ligne->mode_calcul ?? 'auto') === 'manuel'
            && in_array($ligne->rubrique?->calcul_auto, ['CNSS', 'ITS', 'FSSP'], true);
    }

    protected function nullableFloat($valeur): ?float
    {
        return $valeur === null || $valeur === '' ? null : (float) $valeur;
    }

    /**
     * Rubriques applicables au mois calculé : les permanentes et celles « ce mois seulement » de ce mois.
     * Sans mois (aperçu du bulletin modèle), seules les permanentes sont retenues.
     */
    public function lignesDuMois(Collection $lignes, ?int $moisId = null, ?int $anneeId = null): Collection
    {
        return $lignes->filter(function ($l) use ($moisId, $anneeId) {
            if (($l->permanente ?? true) || $l->permanente === null) {
                return true;
            }

            return $moisId !== null
                && (int) $l->mois_id === $moisId
                && ($anneeId === null || $l->annee_id === null || (int) $l->annee_id === $anneeId);
        })->values();
    }

    /**
     * Part imposable d'un gain « Brute I » / « Brute NI » (montant déjà proratisé le cas échéant).
     * Saisie sur la ligne (montant_imposable) ou, à défaut, selon le type : « Brute I » = imposable,
     * « Brute NI » = non imposable. Jamais négative, jamais supérieure au gain.
     */
    /**
     * Part d'un gain soumise à l'ITS et à la CNSS : [ITS, CNSS].
     * Rubrique « soumis_its » / « soumis_cnss » renseignée : tout ou rien. Sinon : part imposable de la ligne
     * (type « Brute I » : tout ; « Brute NI » : rien ; ou part saisie), la CNSS suivant l'ITS.
     */
    public static function partsLigne($ligne, float $montant): array
    {
        $drapeau = function ($v) {
            return $v === null || $v === '' ? null : (bool) $v;
        };

        $its = $drapeau($ligne->rubrique->soumis_its ?? null);
        $partIts = $its === null ? self::partImposable($ligne, $montant) : ($its ? $montant : 0.0);

        $cnss = $drapeau($ligne->rubrique->soumis_cnss ?? null);
        $partCnss = $cnss === null ? $partIts : ($cnss ? $montant : 0.0);

        return [$partIts, $partCnss];
    }

    public static function partImposable($ligne, float $montant): float
    {
        $type = $ligne->rubrique->type_rubrique ?? null;

        if ($ligne->montant_imposable === null || $ligne->montant_imposable === '') {
            return $type === 'Brute I' ? $montant : 0.0;
        }

        $brut = (float) $ligne->montant_gain;
        $part = $brut > 0 ? (float) $ligne->montant_imposable / $brut : 0.0;

        return max(0.0, min($montant, round($montant * $part)));
    }

    protected function parametres(): array
    {
        return $this->parametres ??= [
            'cotisations' => CotisationModel::where('actif', true)->get()
                ->map(fn ($c) => [
                    'taux_salarie'    => (float) $c->taux_salarie,
                    'taux_patronal'   => (float) $c->taux_patronal,
                    'plafond_mensuel' => (float) $c->plafond_mensuel,
                    'organisme'       => $c->organisme ?: 'CNSS',
                ])->all(),
            'abattements' => TauxChargeFamillialeModel::all()
                ->mapWithKeys(fn ($t) => [(int) $t->nombre_de_charge => (float) $t->taux_charge])->all(),
            'tranches_taux' => TauxTrancheFiscaleModel::orderBy('min')->get()
                ->map(fn ($t) => ['min' => (float) $t->min, 'max' => (float) $t->max, 'taux' => (float) $t->taux_fiscale])->all(),
            'tranches_montant' => MontantTrancheFiscaleModel::orderBy('tranche_min')->get()
                ->map(fn ($t) => ['min' => (float) $t->tranche_min, 'max' => (float) $t->tranche_max, 'valeur' => (float) $t->valeur_fiscale])->all(),
        ];
    }
}
