<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Paie du personnel : options par rubrique et par employé.
 *
 *  - mode_calcul        : 'auto' (défaut) ou 'manuel' — pour CNSS / ITS / FSSP, le montant peut être saisi à la main.
 *  - permanente         : true (défaut) = la rubrique s'applique chaque mois ; false = « ce mois seulement ».
 *  - mois_id, annee_id  : mois concerné quand la rubrique n'est pas permanente.
 *  - montant_imposable  : part imposable d'un gain (le reste est non imposable) ; null = selon le type de la rubrique.
 *
 * Les montants imposables sont aussi recopiés sur le bulletin calculé, et les totaux « brut imposable »
 * et « brut non imposable » sont enregistrés sur la fiche de paie.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('model_bulletin_models', function (Blueprint $t) {
            if (! Schema::hasColumn('model_bulletin_models', 'mode_calcul')) {
                $t->string('mode_calcul', 10)->default('auto');
            }
            if (! Schema::hasColumn('model_bulletin_models', 'permanente')) {
                $t->boolean('permanente')->default(true);
            }
            if (! Schema::hasColumn('model_bulletin_models', 'mois_id')) {
                $t->unsignedBigInteger('mois_id')->nullable();
            }
            if (! Schema::hasColumn('model_bulletin_models', 'annee_id')) {
                $t->unsignedBigInteger('annee_id')->nullable();
            }
            if (! Schema::hasColumn('model_bulletin_models', 'montant_imposable')) {
                $t->double('montant_imposable')->nullable();
            }
        });

        Schema::table('bulletin_calculer_models', function (Blueprint $t) {
            if (! Schema::hasColumn('bulletin_calculer_models', 'montant_imposable')) {
                $t->double('montant_imposable')->nullable();
            }
        });

        Schema::table('fiche_paie_models', function (Blueprint $t) {
            if (! Schema::hasColumn('fiche_paie_models', 'brut_imposable')) {
                $t->double('brut_imposable')->nullable();
            }
            if (! Schema::hasColumn('fiche_paie_models', 'brut_non_imposable')) {
                $t->double('brut_non_imposable')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Volontairement vide : ces colonnes peuvent contenir des données de paie.
    }
};
