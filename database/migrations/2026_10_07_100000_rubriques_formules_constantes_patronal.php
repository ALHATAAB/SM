<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Paie : sens « Patronal (hors net) », rubriques automatiques / manuelles à formule, constantes.
 *
 *  - rubrique_models.sens_rubrique : ajoute la valeur « Patronal » (charge employeur, hors net).
 *    Les rubriques CNSS_PAT et ANPE_PAT passent en « Patronal ».
 *  - rubrique_models.mode_rubrique : « manuel » (défaut) ou « automatique ».
 *    Les rubriques déjà pilotées par un barème (calcul_auto) deviennent « automatique ».
 *  - rubrique_models.formule : formule d'une rubrique automatique (ex. base*taux, base*taux*nombre).
 *  - model_bulletin_models.montant_patronal : montant d'une rubrique « Patronal » saisie ou calculée par formule.
 *  - constante_paie_models : constantes utilisables dans les formules (catégories tranche, calcul, test, autre).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Sens « Patronal »
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE rubrique_models MODIFY sens_rubrique ENUM('Gain','Retenu','Patronal') NOT NULL DEFAULT 'Gain'");
        } else {
            Schema::table('rubrique_models', function (Blueprint $t) {
                $t->string('sens_rubrique', 10)->default('Gain')->change();
            });
        }

        // 2. Mode et formule
        Schema::table('rubrique_models', function (Blueprint $t) {
            if (! Schema::hasColumn('rubrique_models', 'mode_rubrique')) {
                $t->string('mode_rubrique', 12)->default('manuel');
            }
            if (! Schema::hasColumn('rubrique_models', 'formule')) {
                $t->string('formule', 255)->nullable();
            }
        });

        DB::table('rubrique_models')->whereNotNull('calcul_auto')->update(['mode_rubrique' => 'automatique']);
        DB::table('rubrique_models')->whereIn('calcul_auto', ['CNSS_PAT', 'ANPE_PAT'])->update(['sens_rubrique' => 'Patronal']);

        // 3. Montant patronal sur le bulletin modèle
        Schema::table('model_bulletin_models', function (Blueprint $t) {
            if (! Schema::hasColumn('model_bulletin_models', 'montant_patronal')) {
                $t->decimal('montant_patronal', 12, 2)->nullable();
            }
        });

        // 4. Constantes
        if (! Schema::hasTable('constante_paie_models')) {
            Schema::create('constante_paie_models', function (Blueprint $t) {
                $t->id();
                $t->string('code', 50)->unique();            // MAJUSCULES, chiffres et _ ; utilisé dans les formules
                $t->string('libelle');
                $t->string('categorie', 10)->default('autre'); // tranche | calcul | test | autre
                $t->decimal('valeur', 16, 4)->default(0);      // tranche : borne minimale
                $t->decimal('valeur_max', 16, 4)->nullable();  // tranche : borne maximale (CODE_MAX dans les formules)
                $t->string('description')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Volontairement vide : ces colonnes et tables peuvent contenir des données de paie.
    }
};
