<?php

namespace App\Models\PaieRH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ParametrePaieModel extends Model
{
    protected $guarded = [];

    protected $table = 'parametre_paie_models';

    /** Valeur numérique d'un réglage ; $defaut si absent (ou si la table n'existe pas encore). */
    public static function nombre(string $cle, float $defaut): float
    {
        if (! Schema::hasTable('parametre_paie_models')) {
            return $defaut;
        }

        $valeur = static::where('cle', $cle)->value('valeur');

        return is_numeric($valeur) ? (float) $valeur : $defaut;
    }

    /** Valeur texte d'un réglage ; $defaut si absent (ou si la table n'existe pas encore). */
    public static function texte(string $cle, string $defaut): string
    {
        if (! Schema::hasTable('parametre_paie_models')) {
            return $defaut;
        }

        $valeur = static::where('cle', $cle)->value('valeur');

        return $valeur !== null && $valeur !== '' ? (string) $valeur : $defaut;
    }

    public static function definir(string $cle, $valeur, ?string $libelle = null): void
    {
        static::updateOrCreate(['cle' => $cle], array_filter(['valeur' => (string) $valeur, 'libelle' => $libelle], fn ($v) => $v !== null));
    }
}
