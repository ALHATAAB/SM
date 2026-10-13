<?php

namespace App\Services;

use App\Models\PaieRH\ConstantePaieModel;
use App\Models\PaieRH\CotisationModel;
use App\Models\PaieRH\MontantTrancheFiscaleModel;
use App\Models\PaieRH\ParametrePaieModel;
use App\Models\PaieRH\TauxChargeFamillialeModel;
use App\Models\PaieRH\TauxTrancheFiscaleModel;
use InvalidArgumentException;

/**
 * Évalue les formules des rubriques de paie, sans eval().
 *
 * Variables de la ligne : base, taux (en %, divisé par 100 : 5 saisi => 0.05), nombre,
 * anciennete (années complètes depuis la date d'embauche), charge_familiale (nombre de charges de l'employé)
 * et taux_charge (taux d'abattement en %, tel que défini dans Paramètres paie pour ce nombre de charges).
 * Paramètres de paie utilisables par leur code (lecture seule, pris dans Paramètres paie) : FSSP_TAUX, CNSS_TAUX_SALARIE,
 * CNSS_TAUX_PATRONAL, CNSS_PLAFOND, ANPE_TAUX_PATRONAL, ANPE_PLAFOND, TAUX_CHARGE_<n>, ITS_T<i>_MIN/MAX/TAUX, ITS_M<i>_MIN/MAX/VALEUR.
 * Les taux des constantes et paramètres sont des pourcentages (1 = 1 %) : on divise par 100 dans la formule.
 * Constantes : leur code (ex. PLAFOND_CNSS) ; pour une constante « tranche », CODE = borne min et CODE_MAX = borne max.
 * Opérateurs : + - * /  ( )   comparaisons : > >= < <= = == != (donnent 1 ou 0).
 * Fonctions  : min(a;b)  max(a;b)  round(x;décimales)  abs(x)  floor(x)  ceil(x)  si(test;alors;sinon).
 * Séparateur d'arguments : ; ou , — décimales avec un point (0.5).
 *
 * Exemples : base*nombre   base*taux   base*taux*nombre   base+nombre   min(base;PLAFOND)*taux
 *            si(base>SEUIL;base*taux;0)
 */
class FormuleService
{
    public const VARIABLES = ['BASE', 'TAUX', 'NOMBRE', 'ANCIENNETE', 'CHARGE_FAMILIALE', 'TAUX_CHARGE'];

    /** Nombres admis écrits « en dur » dans une formule ; tout autre nombre doit être une constante ou un paramètre. */
    public const LITTERAUX_ADMIS = [0.0, 1.0, 100.0];

    protected ?array $parametres = null;

    protected ?array $constantes = null;

    /** Années complètes d'ancienneté à la date donnée (aujourd'hui par défaut) ; 0 si date d'embauche inconnue ou future. */
    public static function anciennete($dateEmbauche, $au = null): int
    {
        if (empty($dateEmbauche)) {
            return 0;
        }

        $debut = \Illuminate\Support\Carbon::parse($dateEmbauche)->startOfDay();
        $fin   = $au ? \Illuminate\Support\Carbon::parse($au)->startOfDay() : \Illuminate\Support\Carbon::today();

        return $debut->greaterThan($fin) ? 0 : (int) floor($debut->diffInYears($fin));
    }

    /** @return array<string,float> code => valeur */
    public function constantes(): array
    {
        if ($this->constantes === null) {
            $this->constantes = [];
            foreach (ConstantePaieModel::all() as $c) {
                $code = strtoupper($c->code);
                $this->constantes[$code] = (float) $c->valeur;
                if ($c->valeur_max !== null) {
                    $this->constantes[$code.'_MAX'] = (float) $c->valeur_max;
                }
            }
            // Paramètres de paie (CNSS, FSSP, charges de famille, barème ITS) : prioritaires sur les constantes
            foreach ($this->parametres() as $code => $p) {
                $this->constantes[$code] = $p['valeur'];
            }
        }

        return $this->constantes;
    }

    /**
     * Paramètres de paie utilisables dans les formules, lus en direct dans Paramètres paie (lecture seule).
     *
     * @return array<string,array{valeur:float,libelle:string}>
     */
    public function parametres(): array
    {
        if ($this->parametres !== null) {
            return $this->parametres;
        }

        $p = [];
        $p['FSSP_TAUX'] = [
            'valeur'  => ParametrePaieModel::nombre('fssp_taux', (float) config('paie.fssp_taux', 1)),
            'libelle' => 'Taux FSSP (% du net)',
        ];

        $p['ITS_FRAIS_PRO_TAUX'] = [
            'valeur'  => ParametrePaieModel::nombre('its_frais_pro_taux', 0.0),
            'libelle' => 'ITS : abattement pour frais professionnels (%)',
        ];

        $cotisations = CotisationModel::where('actif', true)->get();
        $anpe = fn ($c) => strtoupper((string) ($c->organisme ?: 'CNSS')) === 'ANPE';
        $cnss = $cotisations->reject($anpe);
        $ajouts = $cotisations->filter($anpe);

        if ($cnss->isNotEmpty()) {
            $p['CNSS_TAUX_SALARIE']  = ['valeur' => (float) $cnss->sum('taux_salarie'),  'libelle' => 'CNSS : total des taux salariés (%)'];
            $p['CNSS_TAUX_PATRONAL'] = ['valeur' => (float) $cnss->sum('taux_patronal'), 'libelle' => 'CNSS : total des taux patronaux (%)'];
            $p['CNSS_PLAFOND']       = ['valeur' => (float) $cnss->max('plafond_mensuel'), 'libelle' => 'CNSS : plafond mensuel le plus élevé (0 = sans plafond)'];
        }
        // Une paire de codes par branche CNSS : CNSS_<BRANCHE>_TAUX_PAT (patronal) et CNSS_<BRANCHE>_TAUX_SAL (salarié)
        $dejaPris = [];
        foreach ($cnss as $b) {
            $slug = \Illuminate\Support\Str::of((string) $b->intitule)->ascii()->upper()
                ->replaceMatches('/[^A-Z0-9]+/', '_')->trim('_')->limit(22, '')->trim('_')->toString();
            $slug = $slug !== '' ? $slug : 'BRANCHE'.$b->id;
            if (isset($dejaPris[$slug])) {
                $slug .= '_'.$b->id;
            }
            $dejaPris[$slug] = true;
            $p["CNSS_{$slug}_TAUX_PAT"] = ['valeur' => (float) $b->taux_patronal, 'libelle' => "CNSS patronal — {$b->intitule} (%)"];
            $p["CNSS_{$slug}_TAUX_SAL"] = ['valeur' => (float) $b->taux_salarie,  'libelle' => "CNSS salarié — {$b->intitule} (%)"];
        }

        if ($ajouts->isNotEmpty()) {
            $p['ANPE_TAUX_PATRONAL'] = ['valeur' => (float) $ajouts->sum('taux_patronal'), 'libelle' => 'ANPE : total des taux patronaux (%)'];
            $p['ANPE_PLAFOND']       = ['valeur' => (float) $ajouts->max('plafond_mensuel'), 'libelle' => 'ANPE : plafond mensuel (0 = sans plafond)'];
        }

        foreach (TauxChargeFamillialeModel::orderBy('nombre_de_charge')->get() as $r) {
            $n = (int) $r->nombre_de_charge;
            $p['TAUX_CHARGE_'.$n] = ['valeur' => (float) $r->taux_charge, 'libelle' => "Abattement pour {$n} charge(s) de famille (%)"];
        }

        foreach (TauxTrancheFiscaleModel::orderBy('min')->get()->values() as $i => $t) {
            $k = $i + 1;
            $p["ITS_T{$k}_MIN"]  = ['valeur' => (float) $t->min,          'libelle' => "Barème ITS par taux, tranche {$k} : borne min"];
            $p["ITS_T{$k}_MAX"]  = ['valeur' => (float) $t->max,          'libelle' => "Barème ITS par taux, tranche {$k} : borne max"];
            $p["ITS_T{$k}_TAUX"] = ['valeur' => (float) $t->taux_fiscale, 'libelle' => "Barème ITS par taux, tranche {$k} : taux (%)"];
        }
        foreach (MontantTrancheFiscaleModel::orderBy('tranche_min')->get()->values() as $i => $t) {
            $k = $i + 1;
            $p["ITS_M{$k}_MIN"]    = ['valeur' => (float) $t->tranche_min,    'libelle' => "Barème ITS par montant, tranche {$k} : borne min"];
            $p["ITS_M{$k}_MAX"]    = ['valeur' => (float) $t->tranche_max,    'libelle' => "Barème ITS par montant, tranche {$k} : borne max"];
            $p["ITS_M{$k}_VALEUR"] = ['valeur' => (float) $t->valeur_fiscale, 'libelle' => "Barème ITS par montant, tranche {$k} : montant"];
        }

        return $this->parametres = $p;
    }

    /** Un code de constante est réservé s'il désigne une variable de ligne ou un paramètre de paie automatique. */
    public function codeReserve(string $code): bool
    {
        $c = strtoupper(trim($code));

        return in_array($c, self::VARIABLES, true)
            || array_key_exists($c, $this->parametres())
            || preg_match('/^(ITS_|CNSS_|ANPE_|FSSP_|TAUX_CHARGE)/', $c) === 1;
    }

    /**
     * Ce que l'on peut écrire dans une formule, pour l'écran de création des rubriques :
     * variables de la ligne, constantes (par catégorie) et paramètres de paie.
     *
     * @return array<int,array{groupe:string,items:array<int,array{code:string,libelle:string,valeur:?float}>}>
     */
    public function catalogue(): array
    {
        $groupes = [[
            'groupe' => 'Variables de la ligne / de l\'employé',
            'items'  => [
                ['code' => 'base',             'libelle' => 'Base saisie sur la ligne',                        'valeur' => null],
                ['code' => 'taux',             'libelle' => 'Taux saisi sur la ligne (en %, divisé par 100)',   'valeur' => null],
                ['code' => 'nombre',           'libelle' => 'Nombre saisi sur la ligne',                       'valeur' => null],
                ['code' => 'anciennete',       'libelle' => 'Ancienneté en années (date d\'embauche)',         'valeur' => null],
                ['code' => 'charge_familiale', 'libelle' => 'Nombre de charges de famille de l\'employé',      'valeur' => null],
                ['code' => 'taux_charge',      'libelle' => 'Abattement de l\'employé selon ses charges (%)',  'valeur' => null],
            ],
        ]];

        foreach (ConstantePaieModel::CATEGORIES as $cle => $nom) {
            $items = [];
            foreach (ConstantePaieModel::where('categorie', $cle)->orderBy('code')->get() as $c) {
                $items[] = ['code' => strtoupper($c->code), 'libelle' => $c->libelle, 'valeur' => (float) $c->valeur];
                if ($c->valeur_max !== null) {
                    $items[] = ['code' => strtoupper($c->code).'_MAX', 'libelle' => $c->libelle.' (borne max)', 'valeur' => (float) $c->valeur_max];
                }
            }
            if ($items) {
                $groupes[] = ['groupe' => 'Constantes — '.ucfirst($cle), 'items' => $items];
            }
        }

        $items = [];
        foreach ($this->parametres() as $code => $par) {
            $items[] = ['code' => $code, 'libelle' => $par['libelle'], 'valeur' => $par['valeur']];
        }
        if ($items) {
            $groupes[] = ['groupe' => 'Paramètres de paie (automatiques)', 'items' => $items];
        }

        return $groupes;
    }

    /** Constantes et paramètres réellement utilisés par la formule, avec leur valeur actuelle (code => valeur). */
    public function constantesUtilisees(string $formule): array
    {
        try {
            $noms = array_unique($this->identifiants($this->analyser($formule)));
        } catch (InvalidArgumentException) {
            return [];
        }

        $connues = $this->constantes();
        $res = [];
        foreach ($noms as $nom) {
            if (! in_array($nom, self::VARIABLES, true) && array_key_exists($nom, $connues)) {
                $res[$nom] = $connues[$nom];
            }
        }

        return $res;
    }

    /** Variables propres à l'employé : ancienneté, charges de famille et abattement correspondant (Paramètres paie). */
    public static function contexteEmploye($employe): array
    {
        $charges = (int) ($employe->charge_familliale ?? 0);
        $taux = 0.0;
        $trouve = null;
        foreach (TauxChargeFamillialeModel::all() as $r) {
            $n = (int) $r->nombre_de_charge;
            if ($n <= $charges && ($trouve === null || $n > $trouve)) {
                $trouve = $n;
                $taux = (float) $r->taux_charge;
            }
        }

        return [
            'anciennete'       => self::anciennete($employe->date_embauche ?? null),
            'charge_familiale' => $charges,
            'taux_charge'      => $taux,
        ];
    }

    /** Calcule la formule ; lève InvalidArgumentException si elle est incorrecte. */
    public function evaluer(string $formule, array $variables = [], ?array $constantes = null): float
    {
        $ast = $this->analyser($formule);
        $vars = [
            'BASE'   => (float) ($variables['base'] ?? 0),
            'TAUX'   => (float) ($variables['taux'] ?? 0) / 100,
            'NOMBRE' => (float) ($variables['nombre'] ?? 0),
            'ANCIENNETE' => (float) ($variables['anciennete'] ?? 0),
            'CHARGE_FAMILIALE' => (float) ($variables['charge_familiale'] ?? 0),
            'TAUX_CHARGE' => (float) ($variables['taux_charge'] ?? 0),
        ];

        return $this->calculer($ast, $vars + ($constantes ?? $this->constantes()));
    }

    /** Retourne null si la formule est correcte, sinon le message d'erreur. */
    public function valider(string $formule, ?array $constantes = null, bool $sansNombreEnDur = false): ?string
    {
        try {
            $ast = $this->analyser($formule);
            if ($sansNombreEnDur) {
                foreach ($this->litteraux($ast) as $x) {
                    if (! in_array($x, self::LITTERAUX_ADMIS, true)) {
                        return 'Le nombre '.rtrim(rtrim(number_format($x, 4, '.', ''), '0'), '.').' est écrit en dur dans la formule : '
                            .'utilisez une constante (Paramètres paie) ou un paramètre de paie existant. Seuls 0, 1 et 100 sont admis.';
                    }
                }
            }
            $connues = array_merge(self::VARIABLES, array_keys($constantes ?? $this->constantes()));
            foreach ($this->identifiants($ast) as $nom) {
                if (! in_array($nom, $connues, true)) {
                    return "« {$nom} » n'est ni une variable (base, taux, nombre) ni une constante existante.";
                }
            }
            // Essai à blanc avec des valeurs neutres (division par zéro comprise : seul le calcul réel la signale)
            $this->calculer($ast, array_fill_keys($connues, 1.0));
        } catch (InvalidArgumentException $e) {
            return $e->getMessage();
        }

        return null;
    }

    /** Variables de la formule (base, taux, nombre) réellement utilisées : pour n'afficher que les champs utiles. */
    public function variablesUtilisees(string $formule): array
    {
        try {
            $noms = $this->identifiants($this->analyser($formule));
        } catch (InvalidArgumentException) {
            return [];
        }

        return array_values(array_intersect(['BASE', 'TAUX', 'NOMBRE', 'ANCIENNETE', 'CHARGE_FAMILIALE', 'TAUX_CHARGE'], $noms));
    }

    // ------------------------------------------------------------------ analyse

    protected function analyser(string $formule): array
    {
        $jetons = $this->jetons($formule);
        if (empty($jetons)) {
            throw new InvalidArgumentException('La formule est vide.');
        }
        $pos = 0;
        $ast = $this->expression($jetons, $pos);
        if ($pos < count($jetons)) {
            throw new InvalidArgumentException("Formule incorrecte près de « {$jetons[$pos][1]} ».");
        }

        return $ast;
    }

    protected function jetons(string $s): array
    {
        $jetons = [];
        $n = strlen($s);
        $i = 0;
        while ($i < $n) {
            $c = $s[$i];
            if (ctype_space($c)) {
                $i++;
            } elseif (ctype_digit($c) || ($c === '.' && $i + 1 < $n && ctype_digit($s[$i + 1]))) {
                $j = $i;
                while ($j < $n && (ctype_digit($s[$j]) || $s[$j] === '.')) {
                    $j++;
                }
                $txt = substr($s, $i, $j - $i);
                if (substr_count($txt, '.') > 1) {
                    throw new InvalidArgumentException("Nombre incorrect : {$txt}");
                }
                $jetons[] = ['n', $txt];
                $i = $j;
            } elseif (ctype_alpha($c) || $c === '_') {
                $j = $i;
                while ($j < $n && (ctype_alnum($s[$j]) || $s[$j] === '_')) {
                    $j++;
                }
                $jetons[] = ['id', strtoupper(substr($s, $i, $j - $i))];
                $i = $j;
            } else {
                $deux = substr($s, $i, 2);
                if (in_array($deux, ['>=', '<=', '==', '!=', '<>'], true)) {
                    $jetons[] = ['op', $deux === '<>' ? '!=' : $deux];
                    $i += 2;
                } elseif (strpos('+-*/()><=;,', $c) !== false) {
                    $jetons[] = ['op', $c === '=' ? '==' : ($c === ',' ? ';' : $c)];
                    $i++;
                } else {
                    throw new InvalidArgumentException("Caractère non autorisé dans la formule : « {$c} ».");
                }
            }
        }

        return $jetons;
    }

    protected function expression(array $j, int &$p): array
    {
        $g = $this->somme($j, $p);
        if (isset($j[$p]) && $j[$p][0] === 'op' && in_array($j[$p][1], ['>', '>=', '<', '<=', '==', '!='], true)) {
            $op = $j[$p++][1];
            $g = ['b', $op, $g, $this->somme($j, $p)];
        }

        return $g;
    }

    protected function somme(array $j, int &$p): array
    {
        $g = $this->produit($j, $p);
        while (isset($j[$p]) && $j[$p][0] === 'op' && in_array($j[$p][1], ['+', '-'], true)) {
            $op = $j[$p++][1];
            $g = ['b', $op, $g, $this->produit($j, $p)];
        }

        return $g;
    }

    protected function produit(array $j, int &$p): array
    {
        $g = $this->unaire($j, $p);
        while (isset($j[$p]) && $j[$p][0] === 'op' && in_array($j[$p][1], ['*', '/'], true)) {
            $op = $j[$p++][1];
            $g = ['b', $op, $g, $this->unaire($j, $p)];
        }

        return $g;
    }

    protected function unaire(array $j, int &$p): array
    {
        if (isset($j[$p]) && $j[$p][0] === 'op' && $j[$p][1] === '-') {
            $p++;

            return ['u', $this->unaire($j, $p)];
        }

        return $this->primaire($j, $p);
    }

    protected function primaire(array $j, int &$p): array
    {
        if (! isset($j[$p])) {
            throw new InvalidArgumentException('La formule se termine trop tôt.');
        }
        [$type, $val] = $j[$p++];

        if ($type === 'n') {
            return ['n', (float) $val];
        }

        if ($type === 'id') {
            if (isset($j[$p]) && $j[$p] === ['op', '(']) {
                $p++;
                $args = [];
                if (! (isset($j[$p]) && $j[$p] === ['op', ')'])) {
                    do {
                        $args[] = $this->expression($j, $p);
                        $continuer = isset($j[$p]) && $j[$p] === ['op', ';'];
                        if ($continuer) {
                            $p++;
                        }
                    } while ($continuer);
                }
                if (! (isset($j[$p]) && $j[$p] === ['op', ')'])) {
                    throw new InvalidArgumentException("Parenthèse fermante manquante après {$val}(.");
                }
                $p++;
                $this->verifierFonction($val, count($args));

                return ['f', $val, $args];
            }

            return ['v', $val];
        }

        if ($val === '(') {
            $g = $this->expression($j, $p);
            if (! (isset($j[$p]) && $j[$p] === ['op', ')'])) {
                throw new InvalidArgumentException('Parenthèse fermante manquante.');
            }
            $p++;

            return $g;
        }

        throw new InvalidArgumentException("Formule incorrecte près de « {$val} ».");
    }

    protected function verifierFonction(string $nom, int $nb): void
    {
        $attendus = ['MIN' => [2, 2], 'MAX' => [2, 2], 'ROUND' => [1, 2], 'ABS' => [1, 1], 'FLOOR' => [1, 1], 'CEIL' => [1, 1], 'SI' => [3, 3], 'IF' => [3, 3]];
        if (! isset($attendus[$nom])) {
            throw new InvalidArgumentException("Fonction inconnue : {$nom}(). Disponibles : min, max, round, abs, floor, ceil, si.");
        }
        [$min, $max] = $attendus[$nom];
        if ($nb < $min || $nb > $max) {
            throw new InvalidArgumentException("{$nom}() attend ".($min === $max ? $min : "{$min} à {$max}")." argument(s).");
        }
    }

    protected function litteraux(array $n): array
    {
        return match ($n[0]) {
            'n'     => [$n[1]],
            'u'     => $this->litteraux($n[1]),
            'b'     => array_merge($this->litteraux($n[2]), $this->litteraux($n[3])),
            'f'     => array_merge([], ...array_map(fn ($a) => $this->litteraux($a), $n[2])),
            default => [],
        };
    }

    protected function identifiants(array $n): array
    {
        return match ($n[0]) {
            'v'     => [$n[1]],
            'u'     => $this->identifiants($n[1]),
            'b'     => array_merge($this->identifiants($n[2]), $this->identifiants($n[3])),
            'f'     => array_merge([], ...array_map(fn ($a) => $this->identifiants($a), $n[2])),
            default => [],
        };
    }

    // ------------------------------------------------------------------ calcul

    protected function calculer(array $n, array $env): float
    {
        switch ($n[0]) {
            case 'n':
                return $n[1];
            case 'v':
                if (! array_key_exists($n[1], $env)) {
                    throw new InvalidArgumentException("« {$n[1]} » n'est ni une variable (base, taux, nombre) ni une constante existante.");
                }

                return (float) $env[$n[1]];
            case 'u':
                return -$this->calculer($n[1], $env);
            case 'b':
                $a = $this->calculer($n[2], $env);
                $b = $this->calculer($n[3], $env);

                return match ($n[1]) {
                    '+'  => $a + $b,
                    '-'  => $a - $b,
                    '*'  => $a * $b,
                    '/'  => abs($b) < 1e-12 ? throw new InvalidArgumentException('Division par zéro dans la formule.') : $a / $b,
                    '>'  => (float) ($a > $b),
                    '>=' => (float) ($a >= $b),
                    '<'  => (float) ($a < $b),
                    '<=' => (float) ($a <= $b),
                    '==' => (float) (abs($a - $b) < 1e-9),
                    '!=' => (float) (abs($a - $b) >= 1e-9),
                };
            case 'f':
                [, $nom, $args] = $n;
                if ($nom === 'SI' || $nom === 'IF') { // évaluation paresseuse : seule la branche choisie est calculée
                    return $this->calculer($args[0], $env) != 0.0
                        ? $this->calculer($args[1], $env)
                        : $this->calculer($args[2], $env);
                }
                $v = array_map(fn ($a) => $this->calculer($a, $env), $args);

                return match ($nom) {
                    'MIN'   => min($v[0], $v[1]),
                    'MAX'   => max($v[0], $v[1]),
                    'ROUND' => round($v[0], (int) ($v[1] ?? 0)),
                    'ABS'   => abs($v[0]),
                    'FLOOR' => floor($v[0]),
                    'CEIL'  => ceil($v[0]),
                };
        }

        throw new InvalidArgumentException('Formule incorrecte.');
    }
}
