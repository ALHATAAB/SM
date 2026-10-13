<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Corrige : SQLSTATE[42S22] Champ 'etat' inconnu dans where clause (inscription_models).
 * Le code filtre sur `etat` (1 = actif, 0 = annulé) mais les migrations d'origine
 * ne créaient pas cette colonne. Sans danger si elle existe déjà (hasColumn).
 */
return new class extends Migration
{
    private array $tables = [
        'inscription_models',
        'historique_inscription_models',
        'historique_paiement_scolarite_models',
        'historique_abonnement_models',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'etat')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedTinyInteger('etat')->default(1);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'etat')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('etat');
                });
            }
        }
    }
};
