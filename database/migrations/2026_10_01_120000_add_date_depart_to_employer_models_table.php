<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Date de départ du personnel : vide tant que l'employé est en poste.
 * Elle est renseignée à la saisie de la date de fin d'un certificat de travail
 * (voir CertificatService) ou à la main dans la fiche de l'employé.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employer_models') && !Schema::hasColumn('employer_models', 'date_depart')) {
            Schema::table('employer_models', function (Blueprint $table) {
                $table->date('date_depart')->nullable()->after('date_embauche');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employer_models', 'date_depart')) {
            Schema::table('employer_models', function (Blueprint $table) {
                $table->dropColumn('date_depart');
            });
        }
    }
};
