<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes utilisées par le code mais absentes des migrations d'origine
 * (installation neuve : abonnement, vente et dépense échouaient). Chaque ajout est
 * protégé par hasColumn : sans effet si la colonne existe déjà (base de production).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['abonnement_models', 'historique_abonnement_models', 'vente_models'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'remise')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->double('remise')->nullable()->default(0);
                });
            }
        }

        if (Schema::hasTable('operation_depense_models')) {
            foreach (['caisse_id', 'banque_id', 'type_abonnement_id'] as $col) {
                if (! Schema::hasColumn('operation_depense_models', $col)) {
                    Schema::table('operation_depense_models', function (Blueprint $t) use ($col) {
                        $t->unsignedBigInteger($col)->nullable();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // Volontairement vide : ces colonnes peuvent exister en production avant cette migration.
    }
};
