<?php

namespace App\Services;

/**
 * QR code (ISO 18004, modèle 2) généré en SVG : aucune bibliothèque externe, comme Code128.
 * Mode octets (UTF-8), correction d'erreurs niveau M (~15 %), versions 1 à 10 (jusqu'à 213 octets).
 * Validé contre la bibliothèque python « qrcode » et un décodeur OpenCV.
 */
class QrCode
{
    /** Niveau M : version => [codets de correction par bloc, [[nombre de blocs, codets de données par bloc], ...]]. */
    private const VERSIONS = [
        1  => [10, [[1, 16]]],
        2  => [16, [[1, 28]]],
        3  => [26, [[1, 44]]],
        4  => [18, [[2, 32]]],
        5  => [24, [[2, 43]]],
        6  => [16, [[4, 27]]],
        7  => [18, [[4, 31]]],
        8  => [22, [[2, 38], [2, 39]]],
        9  => [22, [[3, 36], [2, 37]]],
        10 => [26, [[4, 43], [1, 44]]],
    ];

    /** Centres des motifs d'alignement (lignes/colonnes) par version. */
    private const ALIGNEMENT = [
        1 => [], 2 => [6, 18], 3 => [6, 22], 4 => [6, 26], 5 => [6, 30],
        6 => [6, 34], 7 => [6, 22, 38], 8 => [6, 24, 42], 9 => [6, 26, 46], 10 => [6, 28, 50],
    ];

    /** Modules restants après le placement des codets (versions 2 à 6 : 7 bits). */
    private const BITS_RESTANTS = [1 => 0, 2 => 7, 3 => 7, 4 => 7, 5 => 7, 6 => 7, 7 => 0, 8 => 0, 9 => 0, 10 => 0];

    /** @var array<int,int> */
    private static array $exp = [];
    /** @var array<int,int> */
    private static array $log = [];

    /* ====================================================================== API */

    /**
     * Matrice des modules : tableau de lignes, chaque module vaut true (noir) ou false (blanc).
     * $masque (0-7) force le masque ; null = meilleur masque automatique.
     *
     * @return array<int,array<int,bool>>
     */
    public static function matrice(string $texte, ?int $masque = null): array
    {
        $octets = array_values(unpack('C*', $texte) ?: []);
        if (!$octets) {
            throw new \InvalidArgumentException('Contenu du QR code vide.');
        }

        $version = self::choisirVersion(count($octets));
        $codets  = self::entrelacer($version, self::donnees($version, $octets));
        $taille  = 17 + 4 * $version;

        // Modules de fonction (motifs fixes) : dessinés une fois, puis on place les données.
        [$base, $fonction] = self::motifsFixes($version, $taille);
        self::placerDonnees($base, $fonction, $codets, $taille, $version);

        $choix = null;
        $meilleur = PHP_INT_MAX;
        foreach ($masque === null ? range(0, 7) : [$masque] as $m) {
            $essai = self::appliquerMasque($base, $fonction, $m, $taille);
            self::ecrireFormat($essai, $m, $taille);
            $score = $masque === null ? self::penalite($essai, $taille) : 0;
            if ($choix === null || $score < $meilleur) {
                $meilleur = $score;
                $choix = $essai;
            }
        }

        return array_map(fn ($ligne) => array_map(fn ($v) => $v === 1, $ligne), $choix);
    }

    public static function svg(string $texte, int $marge = 4): string
    {
        $m = self::matrice($texte);
        $n = count($m);
        $total = $n + 2 * $marge;

        $rects = '';
        foreach ($m as $y => $ligne) {
            $x = 0;
            while ($x < $n) {
                if ($ligne[$x]) {
                    $debut = $x;
                    while ($x < $n && $ligne[$x]) {
                        $x++;
                    }
                    $rects .= '<rect x="' . ($debut + $marge) . '" y="' . ($y + $marge) . '" width="' . ($x - $debut) . '" height="1" fill="#000000"/>';
                } else {
                    $x++;
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $total . '" height="' . $total
            . '" viewBox="0 0 ' . $total . ' ' . $total . '" shape-rendering="crispEdges">'
            . '<rect x="0" y="0" width="' . $total . '" height="' . $total . '" fill="#ffffff"/>' . $rects . '</svg>';
    }

    /** URI de données utilisable dans <img src="...">, affichable par DomPDF comme Code128::dataUri(). */
    public static function dataUri(string $texte, int $marge = 4): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(self::svg($texte, $marge));
    }

    /* ============================================================ encodage */

    private static function choisirVersion(int $nbOctets): int
    {
        foreach (self::VERSIONS as $version => [$ec, $groupes]) {
            $donnees = 0;
            foreach ($groupes as [$blocs, $cw]) {
                $donnees += $blocs * $cw;
            }
            $enTete = 4 + ($version >= 10 ? 16 : 8);
            if ($nbOctets <= intdiv($donnees * 8 - $enTete, 8)) {
                return $version;
            }
        }

        throw new \InvalidArgumentException('Contenu trop long pour le QR code (213 octets maximum).');
    }

    /** @return array<int,int> codets de données (avec remplissage) */
    private static function donnees(int $version, array $octets): array
    {
        $bits = '0100' . str_pad(decbin(count($octets)), $version >= 10 ? 16 : 8, '0', STR_PAD_LEFT);
        foreach ($octets as $o) {
            $bits .= str_pad(decbin($o), 8, '0', STR_PAD_LEFT);
        }

        $capacite = 0;
        foreach (self::VERSIONS[$version][1] as [$blocs, $cw]) {
            $capacite += $blocs * $cw;
        }

        $bits .= str_repeat('0', min(4, $capacite * 8 - strlen($bits)));   // terminateur
        $bits .= str_repeat('0', (8 - strlen($bits) % 8) % 8);            // alignement sur l'octet

        $codets = array_map('bindec', str_split($bits, 8));
        for ($i = 0; count($codets) < $capacite; $i++) {
            $codets[] = $i % 2 === 0 ? 0xEC : 0x11;
        }

        return $codets;
    }

    /** Découpe en blocs, ajoute Reed-Solomon, entrelace données puis correction. @return array<int,int> */
    private static function entrelacer(int $version, array $codets): array
    {
        [$nbEc, $groupes] = self::VERSIONS[$version];

        $blocsDonnees = $blocsEc = [];
        $pos = 0;
        foreach ($groupes as [$nb, $cw]) {
            for ($i = 0; $i < $nb; $i++) {
                $bloc = array_slice($codets, $pos, $cw);
                $pos += $cw;
                $blocsDonnees[] = $bloc;
                $blocsEc[] = self::reedSolomon($bloc, $nbEc);
            }
        }

        $resultat = [];
        $max = max(array_map('count', $blocsDonnees));
        for ($i = 0; $i < $max; $i++) {
            foreach ($blocsDonnees as $bloc) {
                if (isset($bloc[$i])) {
                    $resultat[] = $bloc[$i];
                }
            }
        }
        for ($i = 0; $i < $nbEc; $i++) {
            foreach ($blocsEc as $bloc) {
                $resultat[] = $bloc[$i];
            }
        }

        return $resultat;
    }

    /* ---------------------------------------------------------- Reed-Solomon */

    private static function initCorps(): void
    {
        if (self::$exp) {
            return;
        }
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$exp[$i] = $x;
            self::$log[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11D;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            self::$exp[$i] = self::$exp[$i - 255];
        }
    }

    private static function multiplier(int $a, int $b): int
    {
        return ($a === 0 || $b === 0) ? 0 : self::$exp[self::$log[$a] + self::$log[$b]];
    }

    /** @return array<int,int> codets de correction pour un bloc */
    private static function reedSolomon(array $donnees, int $nbEc): array
    {
        self::initCorps();

        // Polynôme générateur : produit des (x - alpha^i), i = 0 .. nbEc-1 (coefficients de haut en bas, sans le 1 de tête)
        $gen = [1];
        for ($i = 0; $i < $nbEc; $i++) {
            $suivant = array_fill(0, count($gen) + 1, 0);
            foreach ($gen as $j => $c) {
                $suivant[$j] ^= $c;
                $suivant[$j + 1] ^= self::multiplier($c, self::$exp[$i]);
            }
            $gen = $suivant;
        }

        $reste = array_fill(0, $nbEc, 0);
        foreach ($donnees as $octet) {
            $facteur = $octet ^ array_shift($reste);
            $reste[] = 0;
            if ($facteur !== 0) {
                for ($j = 0; $j < $nbEc; $j++) {
                    $reste[$j] ^= self::multiplier($gen[$j + 1], $facteur);
                }
            }
        }

        return $reste;
    }

    /* ========================================================= la matrice */

    /** @return array{0: array<int,array<int,int>>, 1: array<int,array<int,bool>>} modules et masque des modules de fonction */
    private static function motifsFixes(int $version, int $taille): array
    {
        $m = array_fill(0, $taille, array_fill(0, $taille, 0));
        $f = array_fill(0, $taille, array_fill(0, $taille, false));

        $poser = function (int $x, int $y, bool $noir) use (&$m, &$f, $taille) {
            if ($x >= 0 && $x < $taille && $y >= 0 && $y < $taille) {
                $m[$y][$x] = $noir ? 1 : 0;
                $f[$y][$x] = true;
            }
        };

        // Lignes de synchronisation (dessinées d'abord : les repères les recouvrent)
        for ($i = 0; $i < $taille; $i++) {
            $poser(6, $i, $i % 2 === 0);
            $poser($i, 6, $i % 2 === 0);
        }

        // Motifs de repérage + séparateurs
        foreach ([[3, 3], [$taille - 4, 3], [3, $taille - 4]] as [$cx, $cy]) {
            for ($dy = -4; $dy <= 4; $dy++) {
                for ($dx = -4; $dx <= 4; $dx++) {
                    $d = max(abs($dx), abs($dy));
                    $poser($cx + $dx, $cy + $dy, $d !== 2 && $d !== 4);
                }
            }
        }

        // Motifs d'alignement
        $pos = self::ALIGNEMENT[$version];
        $n = count($pos);
        foreach ($pos as $i => $cy) {
            foreach ($pos as $j => $cx) {
                if (($i === 0 && $j === 0) || ($i === 0 && $j === $n - 1) || ($i === $n - 1 && $j === 0)) {
                    continue; // recouvre un motif de repérage
                }
                for ($dy = -2; $dy <= 2; $dy++) {
                    for ($dx = -2; $dx <= 2; $dx++) {
                        $poser($cx + $dx, $cy + $dy, max(abs($dx), abs($dy)) !== 1);
                    }
                }
            }
        }

        // Zones de format (écrites plus tard) et module noir fixe
        for ($i = 0; $i < 9; $i++) {
            $f[8][$i] = $f[$i][8] = true;
        }
        for ($i = 0; $i < 8; $i++) {
            $f[8][$taille - 1 - $i] = $f[$taille - 1 - $i][8] = true;
        }
        $poser(8, $taille - 8, true);

        // Information de version (version 7 et plus)
        if ($version >= 7) {
            $reste = $version;
            for ($i = 0; $i < 12; $i++) {
                $reste = ($reste << 1) ^ (($reste >> 11) * 0x1F25);
            }
            $bits = ($version << 12) | $reste;
            for ($i = 0; $i < 18; $i++) {
                $noir = (($bits >> $i) & 1) === 1;
                $a = $taille - 11 + $i % 3;
                $b = intdiv($i, 3);
                $poser($a, $b, $noir);
                $poser($b, $a, $noir);
            }
        }

        return [$m, $f];
    }

    /** Place les bits des codets en zigzag, par paires de colonnes, en partant du coin bas-droit. */
    private static function placerDonnees(array &$m, array $f, array $codets, int $taille, int $version): void
    {
        $bits = '';
        foreach ($codets as $c) {
            $bits .= str_pad(decbin($c), 8, '0', STR_PAD_LEFT);
        }
        $bits .= str_repeat('0', self::BITS_RESTANTS[$version]);

        $i = 0;
        $total = strlen($bits);
        for ($droite = $taille - 1; $droite >= 1; $droite -= 2) {
            if ($droite === 6) {
                $droite = 5; // la colonne de synchronisation est sautée
            }
            for ($v = 0; $v < $taille; $v++) {
                for ($j = 0; $j < 2; $j++) {
                    $x = $droite - $j;
                    $montant = (($droite + 1) & 2) === 0;
                    $y = $montant ? $taille - 1 - $v : $v;
                    if (!$f[$y][$x] && $i < $total) {
                        $m[$y][$x] = (int) $bits[$i++];
                    }
                }
            }
        }
    }

    private static function appliquerMasque(array $m, array $f, int $masque, int $taille): array
    {
        for ($y = 0; $y < $taille; $y++) {
            for ($x = 0; $x < $taille; $x++) {
                if ($f[$y][$x]) {
                    continue;
                }
                $inverser = match ($masque) {
                    0 => ($x + $y) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($x + $y) % 3 === 0,
                    4 => (intdiv($x, 3) + intdiv($y, 2)) % 2 === 0,
                    5 => $x * $y % 2 + $x * $y % 3 === 0,
                    6 => ($x * $y % 2 + $x * $y % 3) % 2 === 0,
                    default => (($x + $y) % 2 + $x * $y % 3) % 2 === 0,
                };
                if ($inverser) {
                    $m[$y][$x] ^= 1;
                }
            }
        }

        return $m;
    }

    /** Information de format : niveau M (00) + masque, code BCH(15,5). */
    private static function ecrireFormat(array &$m, int $masque, int $taille): void
    {
        $donnees = $masque; // niveau M = 00
        $reste = $donnees;
        for ($i = 0; $i < 10; $i++) {
            $reste = ($reste << 1) ^ (($reste >> 9) * 0x537);
        }
        $bits = (($donnees << 10) | $reste) ^ 0x5412;
        $bit = fn (int $i): int => ($bits >> $i) & 1;

        for ($i = 0; $i <= 5; $i++) {
            $m[$i][8] = $bit($i);
        }
        $m[7][8] = $bit(6);
        $m[8][8] = $bit(7);
        $m[8][7] = $bit(8);
        for ($i = 9; $i < 15; $i++) {
            $m[8][14 - $i] = $bit($i);
        }

        for ($i = 0; $i < 8; $i++) {
            $m[8][$taille - 1 - $i] = $bit($i);
        }
        for ($i = 8; $i < 15; $i++) {
            $m[$taille - 15 + $i][8] = $bit($i);
        }
        $m[$taille - 8][8] = 1; // module noir fixe
    }

    /* ---------------------------------------------------------- pénalités */

    private static function penalite(array $m, int $taille): int
    {
        $score = 0;

        // N1 : séries de 5 modules identiques ou plus (lignes puis colonnes)
        for ($pass = 0; $pass < 2; $pass++) {
            for ($a = 0; $a < $taille; $a++) {
                $serie = 1;
                for ($b = 1; $b < $taille; $b++) {
                    $courant = $pass === 0 ? $m[$a][$b] : $m[$b][$a];
                    $precedent = $pass === 0 ? $m[$a][$b - 1] : $m[$b - 1][$a];
                    if ($courant === $precedent) {
                        $serie++;
                        if ($serie === 5) {
                            $score += 3;
                        } elseif ($serie > 5) {
                            $score++;
                        }
                    } else {
                        $serie = 1;
                    }
                }
            }
        }

        // N2 : blocs 2 x 2 de même couleur
        for ($y = 0; $y < $taille - 1; $y++) {
            for ($x = 0; $x < $taille - 1; $x++) {
                $c = $m[$y][$x];
                if ($c === $m[$y][$x + 1] && $c === $m[$y + 1][$x] && $c === $m[$y + 1][$x + 1]) {
                    $score += 3;
                }
            }
        }

        // N3 : motifs ressemblant aux repères (1011101 encadré de 4 modules blancs)
        $motifs = [[1, 0, 1, 1, 1, 0, 1, 0, 0, 0, 0], [0, 0, 0, 0, 1, 0, 1, 1, 1, 0, 1]];
        for ($pass = 0; $pass < 2; $pass++) {
            for ($a = 0; $a < $taille; $a++) {
                for ($b = 0; $b <= $taille - 11; $b++) {
                    foreach ($motifs as $motif) {
                        $ok = true;
                        for ($k = 0; $k < 11; $k++) {
                            $v = $pass === 0 ? $m[$a][$b + $k] : $m[$b + $k][$a];
                            if ($v !== $motif[$k]) {
                                $ok = false;
                                break;
                            }
                        }
                        if ($ok) {
                            $score += 40;
                        }
                    }
                }
            }
        }

        // N4 : équilibre noir / blanc
        $noirs = 0;
        foreach ($m as $ligne) {
            $noirs += array_sum($ligne);
        }
        $total = $taille * $taille;
        $k = (int) ceil(abs($noirs * 20 - $total * 10) / $total) - 1;
        $score += max(0, $k) * 10;

        return $score;
    }
}
