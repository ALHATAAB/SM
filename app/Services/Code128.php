<?php

namespace App\Services;

/**
 * Code-barres Code 128 (jeu B, caractères ASCII 32 à 126), généré en SVG : aucune bibliothèque externe.
 * Table des motifs validée contre python-barcode.
 */
class Code128
{
    /** Largeurs alternées barre/espace des 107 symboles (0-102 données, 103-105 départs, 106 arrêt). */
    private const MOTIFS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112',
    ];

    /** Suite de modules : « 1 » = barre, « 0 » = espace. */
    public static function modules(string $texte): string
    {
        $texte = preg_replace('/[^\x20-\x7E]/', '', $texte);

        $codes = [104]; // départ jeu B
        $somme = 104;
        foreach (str_split($texte) as $i => $c) {
            $valeur = ord($c) - 32;
            $codes[] = $valeur;
            $somme += $valeur * ($i + 1);
        }
        $codes[] = $somme % 103;
        $codes[] = 106; // arrêt

        $modules = '';
        foreach ($codes as $code) {
            $barre = true;
            foreach (str_split(self::MOTIFS[$code]) as $largeur) {
                $modules .= str_repeat($barre ? '1' : '0', (int) $largeur);
                $barre = !$barre;
            }
        }

        return $modules;
    }

    public static function svg(string $texte, int $hauteur = 40): string
    {
        $modules = self::modules($texte);
        $marge   = 10; // zone de silence, en modules
        $largeur = strlen($modules) + 2 * $marge;

        $barres = '';
        $x = $marge;
        foreach (preg_split('/(?<=1)(?=0)|(?<=0)(?=1)/', $modules) as $suite) {
            $n = strlen($suite);
            if ($suite[0] === '1') {
                $barres .= '<rect x="' . $x . '" y="0" width="' . $n . '" height="' . $hauteur . '" fill="#000000"/>';
            }
            $x += $n;
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $largeur . '" height="' . $hauteur
            . '" viewBox="0 0 ' . $largeur . ' ' . $hauteur . '" preserveAspectRatio="none">' . $barres . '</svg>';
    }

    /** URI de données utilisable dans <img src="..."> (DomPDF ≥ 1 sait afficher le SVG). */
    public static function dataUri(string $texte, int $hauteur = 40): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(self::svg($texte, $hauteur));
    }
}
