<?php

namespace App\Services;

/** Convertit un montant entier en toutes lettres (français) : 1 250 000 => « un million deux cent cinquante mille ». */
class MontantEnLettres
{
    private const UNITES = [
        'zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix',
        'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf',
    ];

    private const DIZAINES = [2 => 'vingt', 3 => 'trente', 4 => 'quarante', 5 => 'cinquante', 6 => 'soixante'];

    public static function convertir($nombre): string
    {
        $n = (int) round((float) $nombre);

        if ($n === 0) {
            return self::UNITES[0];
        }
        if ($n < 0) {
            return 'moins ' . self::convertir(-$n);
        }

        $milliards = intdiv($n, 1000000000);
        $n %= 1000000000;
        $millions = intdiv($n, 1000000);
        $n %= 1000000;
        $milliers = intdiv($n, 1000);
        $reste = $n % 1000;

        $mots = [];
        if ($milliards > 0) {
            $mots[] = self::sousMille($milliards, true) . ' milliard' . ($milliards > 1 ? 's' : '');
        }
        if ($millions > 0) {
            $mots[] = self::sousMille($millions, true) . ' million' . ($millions > 1 ? 's' : '');
        }
        if ($milliers > 0) {
            // « mille » est invariable et n'impose pas le pluriel de « cent » ni de « vingt » devant lui
            $mots[] = $milliers === 1 ? 'mille' : self::sousMille($milliers, false) . ' mille';
        }
        if ($reste > 0) {
            $mots[] = self::sousMille($reste, true);
        }

        return implode(' ', $mots);
    }

    /** « Arrêté à la somme de : … francs CFA » avec majuscule initiale. */
    public static function phrase($nombre, ?string $monnaie = null): string
    {
        $monnaie = $monnaie ?? config('documents.monnaie_lettres', 'francs CFA');

        return ucfirst(self::convertir($nombre)) . ' ' . $monnaie;
    }

    /** 0 à 999. $pluriel : « cents » / « quatre-vingts » prennent le s s'ils terminent le groupe. */
    private static function sousMille(int $n, bool $pluriel): string
    {
        $centaines = intdiv($n, 100);
        $reste = $n % 100;
        $mots = [];

        if ($centaines === 1) {
            $mots[] = 'cent';
        } elseif ($centaines > 1) {
            $mots[] = self::UNITES[$centaines] . ' cent' . (($reste === 0 && $pluriel) ? 's' : '');
        }

        if ($reste > 0) {
            $mots[] = self::sousCent($reste, $pluriel);
        }

        return implode(' ', $mots);
    }

    /** 1 à 99. */
    private static function sousCent(int $n, bool $pluriel): string
    {
        if ($n < 20) {
            return self::UNITES[$n];
        }

        $dizaine = intdiv($n, 10);
        $unite = $n % 10;

        if ($dizaine === 7) {
            return $unite === 1 ? 'soixante et onze' : 'soixante-' . self::UNITES[10 + $unite];
        }
        if ($dizaine === 8) {
            return $unite === 0 ? 'quatre-vingt' . ($pluriel ? 's' : '') : 'quatre-vingt-' . self::UNITES[$unite];
        }
        if ($dizaine === 9) {
            return 'quatre-vingt-' . self::UNITES[10 + $unite];
        }

        $base = self::DIZAINES[$dizaine];
        if ($unite === 0) {
            return $base;
        }

        return $unite === 1 ? $base . ' et un' : $base . '-' . self::UNITES[$unite];
    }
}
