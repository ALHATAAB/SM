<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Les livres achetés / vendus sont filtrés par année scolaire
 * (LivreAchete, LivreVendu, NouvelleVente), mais la colonne
 * annee_scolaire_id n'existait pas sur achat_models.
 * Les lignes existantes sont rattachées à l'année actuellement active.
 * Chaque ajout est gardé par hasColumn : sans danger si la colonne existe déjà.
 */
return new class extends Migration
{
    public function up(): void
    {
        $anneeActiveId = DB::table('annee_scolaire_models')->where('etat', 'actif')->value('id');

        foreach (['achat_models', 'vente_models'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'annee_scolaire_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('annee_scolaire_id')->nullable()->index();
                });

                if ($anneeActiveId) {
                    DB::table($tableName)->whereNull('annee_scolaire_id')->update(['annee_scolaire_id' => $anneeActiveId]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['achat_models', 'vente_models'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'annee_scolaire_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['annee_scolaire_id']);
                    $table->dropColumn('annee_scolaire_id');
                });
            }
        }
    }
};
