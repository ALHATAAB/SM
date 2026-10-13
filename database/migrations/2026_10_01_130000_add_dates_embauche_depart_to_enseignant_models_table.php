<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dates d'embauche et de départ des enseignants (comme pour le personnel).
 *  - date_embauche : les enseignants existants reçoivent la date de leur création
 *    (c'était déjà la valeur proposée par le certificat de travail) ;
 *  - date_depart : vide tant que l'enseignant est en poste ; renseignée à la saisie
 *    de la date de fin d'un certificat de travail ou dans la fiche de l'enseignant.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('enseignant_models')) {
            return;
        }

        Schema::table('enseignant_models', function (Blueprint $table) {
            if (!Schema::hasColumn('enseignant_models', 'date_embauche')) {
                $table->date('date_embauche')->nullable();
            }
            if (!Schema::hasColumn('enseignant_models', 'date_depart')) {
                $table->date('date_depart')->nullable();
            }
        });

        foreach (DB::table('enseignant_models')->whereNull('date_embauche')->get(['id', 'created_at']) as $e) {
            if ($e->created_at) {
                DB::table('enseignant_models')->where('id', $e->id)
                    ->update(['date_embauche' => substr((string) $e->created_at, 0, 10)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('enseignant_models', function (Blueprint $table) {
            foreach (['date_embauche', 'date_depart'] as $col) {
                if (Schema::hasColumn('enseignant_models', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
