<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Règles liées à la date de départ du personnel et des enseignants :
 * après cette date on n'émarge plus et on ne calcule plus de paie.
 *
 * Le mois de départ lui-même reste calculé (la personne a travaillé une partie du mois) ;
 * seuls les mois qui commencent APRÈS la date de départ sont exclus.
 */
class DepartService
{
    /** La date (ex. d'un émargement) tombe-t-elle strictement après la date de départ ? */
    public static function dateApresDepart($dateDepart, $date): bool
    {
        if (empty($dateDepart) || empty($date)) {
            return false;
        }

        return Carbon::parse($date)->startOfDay()->gt(Carbon::parse($dateDepart)->startOfDay());
    }

    /** Le mois de paie commence-t-il strictement après la date de départ ? */
    public static function moisApresDepart($dateDepart, int $mois, $anneeScolaire): bool
    {
        if (empty($dateDepart)) {
            return false;
        }

        return self::premierJourDuMois($mois, $anneeScolaire)->gt(Carbon::parse($dateDepart)->startOfDay());
    }

    /**
     * Part du mois travaillée, en jours calendaires, pour le calcul de la paie au prorata :
     *  - 1.0 si la personne n'est pas partie, ou part le dernier jour du mois (ou après) ;
     *  - jours travaillés / jours du mois si elle part en cours de mois (le jour du départ compte) ;
     *  - 0.0 pour un mois qui commence après le départ.
     */
    public static function ratioTravail($dateDepart, int $mois, $anneeScolaire): float
    {
        if (empty($dateDepart)) {
            return 1.0;
        }

        $depart = Carbon::parse($dateDepart)->startOfDay();
        $premier = self::premierJourDuMois($mois, $anneeScolaire);

        if ($premier->gt($depart)) {
            return 0.0;
        }

        $jours = $premier->daysInMonth;

        return $depart->gte($premier->copy()->endOfMonth()->startOfDay())
            ? 1.0
            : $depart->day / $jours;
    }

    /**
     * Premier jour du mois (1-12) dans l'année scolaire : l'année scolaire chevauche deux
     * années civiles, le mois n'a donc pas toujours l'année de début.
     */
    public static function premierJourDuMois(int $mois, $anneeScolaire): Carbon
    {
        $debut = $anneeScolaire?->date_debut ? Carbon::parse($anneeScolaire->date_debut)->startOfDay() : null;
        $fin = $anneeScolaire?->date_fin ? Carbon::parse($anneeScolaire->date_fin)->endOfDay() : null;

        if (!$debut) {
            return Carbon::create((int) ($anneeScolaire?->annee ?: now()->year), $mois, 1)->startOfDay();
        }

        if ($fin) {
            for ($annee = $debut->year; $annee <= $fin->year; $annee++) {
                $premier = Carbon::create($annee, $mois, 1)->startOfDay();

                if ($premier->copy()->endOfMonth()->gte($debut) && $premier->lte($fin)) {
                    return $premier;
                }
            }
        }

        // Mois hors des dates de l'année scolaire : même année que le début s'il le suit, sinon la suivante
        return Carbon::create($mois >= $debut->month ? $debut->year : $debut->year + 1, $mois, 1)->startOfDay();
    }
}
