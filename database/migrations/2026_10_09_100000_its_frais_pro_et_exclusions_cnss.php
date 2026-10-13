<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Calcul de la paie conforme à la feuille « simulation_bulletin » de l'établissement.
 *
 *  - parametre_paie_models.its_frais_pro_taux : abattement ITS pour frais professionnels (13 % de brut − CNSS).
 *  - parametre_paie_models.cnss_patronal_base : base des charges patronales (CNSS / ANPE) : 'brut' (total des gains,
 *    plafonné) ou 'cnss' (même base que la retenue salariale).
 *  - rubrique_models.soumis_cnss / soumis_its : une prime peut être exclue de la CNSS tout en restant soumise à l'ITS
 *    (ex. indemnité de transport), ou l'inverse. NULL = selon le type de rubrique (Brute I : soumise aux deux ;
 *    Brute NI : aucune des deux).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rubrique_models', function (Blueprint $t) {
            if (! Schema::hasColumn('rubrique_models', 'soumis_cnss')) {
                $t->boolean('soumis_cnss')->nullable();
            }
            if (! Schema::hasColumn('rubrique_models', 'soumis_its')) {
                $t->boolean('soumis_its')->nullable();
            }
        });

        $now = now();
        foreach ([
            ['its_frais_pro_taux', '13',   'Abattement ITS pour frais professionnels (% de brut − CNSS)'],
            ['cnss_patronal_base', 'brut', 'Base des charges patronales : brut (total des gains) ou cnss (base salariale)'],
        ] as [$cle, $valeur, $libelle]) {
            if (! DB::table('parametre_paie_models')->where('cle', $cle)->exists()) {
                DB::table('parametre_paie_models')->insert([
                    'cle' => $cle, 'valeur' => $valeur, 'libelle' => $libelle, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Volontairement vide : paramètres de paie.
    }
};
