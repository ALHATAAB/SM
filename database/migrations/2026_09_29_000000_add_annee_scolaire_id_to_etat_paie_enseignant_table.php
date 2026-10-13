<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La génération de la paie enseignant (GestionPaieEnseignant) et les bulletins
 * de paie filtrent sur etat_paie_enseignant_models.annee_scolaire_id, alors que
 * la table d'origine ne contenait que « annee » (l'année en 4 chiffres).
 *
 * On ajoute la colonne si elle manque (sans effet si elle existe déjà) et on
 * rattache les états existants à l'année scolaire correspondante.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('etat_paie_enseignant_models')) {
            return;
        }

        if (!Schema::hasColumn('etat_paie_enseignant_models', 'annee_scolaire_id')) {
            Schema::table('etat_paie_enseignant_models', function (Blueprint $table) {
                $table->unsignedBigInteger('annee_scolaire_id')->nullable()->index();
            });
        }

        if (Schema::hasColumn('etat_paie_enseignant_models', 'annee')) {
            foreach (DB::table('annee_scolaire_models')->get(['id', 'annee']) as $annee) {
                DB::table('etat_paie_enseignant_models')
                    ->whereNull('annee_scolaire_id')
                    ->where('annee', $annee->annee)
                    ->update(['annee_scolaire_id' => $annee->id]);
            }
        }
    }

    public function down(): void
    {
        // Volontairement sans effet : la colonne peut préexister dans la base et être utilisée.
    }
};
