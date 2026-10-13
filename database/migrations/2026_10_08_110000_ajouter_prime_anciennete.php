<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rubrique « Prime d'ancienneté » calculée avec des constantes (modifiables dans Paramètres paie).
 *
 *   si(anciennete>=ANC_SEUIL; base*min(anciennete*ANC_TAUX_AN; ANC_PLAFOND)/100; 0)
 *
 *  - anciennete   : années complètes depuis la date d'embauche (automatique)
 *  - base         : salaire de base saisi sur la ligne de l'employé
 *  - ANC_SEUIL    : ancienneté minimale (années)           — constante « test »
 *  - ANC_TAUX_AN  : % du salaire de base par année         — constante « calcul »
 *  - ANC_PLAFOND  : plafond de la prime en % du salaire    — constante « calcul »
 *
 * Les valeurs initiales (2 ans, 1 % par an, plafond 25 %) sont des valeurs de départ à ajuster
 * selon la convention collective / le règlement de l'établissement.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $constantes = [
            ['ANC_SEUIL',   'Ancienneté minimale (années) pour la prime d\'ancienneté', 'test',   2],
            ['ANC_TAUX_AN', 'Prime d\'ancienneté : % du salaire de base par année',      'calcul', 1],
            ['ANC_PLAFOND', 'Prime d\'ancienneté : plafond en % du salaire de base',     'calcul', 25],
        ];
        foreach ($constantes as [$code, $libelle, $categorie, $valeur]) {
            if (!DB::table('constante_paie_models')->where('code', $code)->exists()) {
                DB::table('constante_paie_models')->insert([
                    'code' => $code, 'libelle' => $libelle, 'categorie' => $categorie, 'valeur' => $valeur,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if (!DB::table('rubrique_models')->where('rubrique', 'Prime d\'ancienneté')->exists()) {
            DB::table('rubrique_models')->insert([
                'code_rubrique'      => ((int) DB::table('rubrique_models')->max('code_rubrique')) + 1,
                'rubrique'           => 'Prime d\'ancienneté',
                'type_rubrique'      => 'Brute I',
                'sens_rubrique'      => 'Gain',
                'categorie_rubrique' => 'Personnel',
                'mode_rubrique'      => 'automatique',
                'formule'            => 'si(anciennete>=ANC_SEUIL;base*min(anciennete*ANC_TAUX_AN;ANC_PLAFOND)/100;0)',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Volontairement vide : la rubrique peut déjà être utilisée sur des bulletins.
    }
};
