<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Chaque licence est rattachée à une année scolaire : le quota (inscrits,
 * utilisateurs) appliqué est celui de la licence de l'année active.
 *
 * Les licences déjà actives (STATUS = 1) sont rattachées à l'année
 * actuellement active, pour que l'installation continue de fonctionner
 * sans réimporter de licence.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('licence') && !Schema::hasColumn('licence', 'annee_scolaire_id')) {
            Schema::table('licence', function (Blueprint $table) {
                $table->unsignedBigInteger('annee_scolaire_id')->nullable()->after('STATUS')->index();
            });

            $anneeActiveId = DB::table('annee_scolaire_models')->where('etat', 'actif')->value('id');

            if ($anneeActiveId) {
                DB::table('licence')
                    ->where('STATUS', 1)
                    ->whereNull('annee_scolaire_id')
                    ->update(['annee_scolaire_id' => $anneeActiveId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('licence') && Schema::hasColumn('licence', 'annee_scolaire_id')) {
            Schema::table('licence', function (Blueprint $table) {
                $table->dropIndex(['annee_scolaire_id']);
                $table->dropColumn('annee_scolaire_id');
            });
        }
    }
};
