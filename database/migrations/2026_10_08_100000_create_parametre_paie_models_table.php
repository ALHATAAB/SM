<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Réglages simples de la paie modifiables depuis « Paramètres paie » (clé / valeur).
 * Première clé : fssp_taux (% du net avant FSSP), initialisée avec la valeur actuelle de config/paie.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('parametre_paie_models')) {
            Schema::create('parametre_paie_models', function (Blueprint $t) {
                $t->id();
                $t->string('cle', 50)->unique();
                $t->string('valeur');
                $t->string('libelle')->nullable();
                $t->timestamps();
            });
        }

        if (! DB::table('parametre_paie_models')->where('cle', 'fssp_taux')->exists()) {
            DB::table('parametre_paie_models')->insert([
                'cle'        => 'fssp_taux',
                'valeur'     => (string) (float) config('paie.fssp_taux', 1),
                'libelle'    => 'Taux FSSP (% du salaire net avant FSSP)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Volontairement vide : paramètres de paie.
    }
};
