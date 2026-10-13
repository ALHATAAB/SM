<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CNSS part patronale : charge de l'employeur, calculée avec les taux patronaux du
     * barème CNSS (Paramètre paie RH). Elle est informative : jamais déduite du net du salarié.
     * Pour un mois déjà calculé, relancer « Calculer » (fiche non payée) renseigne la colonne.
     */
    public function up(): void
    {
        Schema::table('fiche_paie_models', function (Blueprint $table) {
            $table->decimal('cnss_patronal', 12, 2)->default(0)->after('total_retenu');
        });
    }

    public function down(): void
    {
        Schema::table('fiche_paie_models', function (Blueprint $table) {
            $table->dropColumn('cnss_patronal');
        });
    }
};
