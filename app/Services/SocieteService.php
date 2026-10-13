<?php

namespace App\Services;

use App\Models\Config\SocieteModel;
use Illuminate\Support\Facades\Storage;

/**
 * Source unique des informations de la société (paramètre « Société ») :
 * entête des documents PDF, logo du site, etc.
 * Le logo enregistré remplace le logo par défaut dès qu'il existe sur le disque.
 */
class SocieteService
{
    public const LOGO_DEFAUT = 'assets/img/logos/logo_smartschool.png';

    protected static ?array $cache = null;

    /** Efface le cache (à appeler après l'enregistrement des paramètres société). */
    public static function oublier(): void
    {
        self::$cache = null;
    }

    public static function info(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $s = SocieteModel::first();

        $contact = array_filter([
            !empty($s?->telephone) ? 'Tél : ' . $s->telephone : null,
            !empty($s?->email) ? 'Email : ' . $s->email : null,
        ]);

        $legal = array_filter([
            !empty($s?->nif) ? 'NIF : ' . $s->nif : null,
            !empty($s?->rccm) ? 'RCCM : ' . $s->rccm : null,
        ]);

        $adresse = trim(implode(' ', array_filter([$s?->adresse, $s?->complement, $s?->code_postal])));

        return self::$cache = [
            'nom'        => $s?->raison_sociale ?: 'SMART SCHOOL ALHATAAB',
            'activite'   => $s?->activite,
            'forme'      => $s?->forme_juridique,
            'adresse'    => $adresse ?: 'Niamey, Niger',
            'telephone'  => $s?->telephone,
            'email'      => $s?->email,
            'site_web'   => $s?->site_web,
            'contact'    => implode(' - ', $contact),
            'legal'      => implode(' - ', $legal),
            'logo'       => self::logoChemin($s?->logo),
            'logo_url'   => self::logoUrl($s?->logo),
        ];
    }

    /** Chemin absolu du logo (pour DomPDF) : logo enregistré s'il existe, sinon logo par défaut. */
    protected static function logoChemin(?string $logo): string
    {
        if ($logo && Storage::disk('public')->exists($logo)) {
            return Storage::disk('public')->path($logo);
        }

        return public_path(self::LOGO_DEFAUT);
    }

    /** URL du logo (pour les pages web). Un paramètre de version évite le cache du navigateur. */
    protected static function logoUrl(?string $logo): string
    {
        if ($logo && Storage::disk('public')->exists($logo)) {
            return route('societe.logo', ['v' => Storage::disk('public')->lastModified($logo)]);
        }

        return asset(self::LOGO_DEFAUT);
    }
}
