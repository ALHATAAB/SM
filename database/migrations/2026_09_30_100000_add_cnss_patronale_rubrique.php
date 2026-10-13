<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CNSS part patronale = vraie ligne du bulletin (base, taux, montant), comme la part salariale,
     * mais stockée dans sa propre colonne `montant_patronal` : elle n'est ni un gain ni une retenue,
     * donc jamais comptée dans total_gain / total_retenu / salaire_net.
     *
     * La rubrique « CNSS_PAT » n'est rattachée à aucun bulletin modèle : elle est générée
     * automatiquement avec la CNSS salariale lors du calcul de l'état de paie.
     * (sens_rubrique est un ENUM Gain/Retenu : on garde « Retenu » ici, c'est le champ
     * calcul_auto = CNSS_PAT qui la distingue.)
     */
    public function up(): void
    {
        Schema::table('bulletin_calculer_models', function (Blueprint $table) {
            $table->decimal('montant_patronal', 12, 2)->nullable()->after('montant_retenu');
        });

        if (!DB::table('rubrique_models')->where('calcul_auto', 'CNSS_PAT')->exists()) {
            $now = now();
            DB::table('rubrique_models')->insert([
                'code_rubrique'      => ((int) DB::table('rubrique_models')->max('code_rubrique')) + 1,
                'rubrique'           => 'CNSS (part patronale)',
                'type_rubrique'      => 'Cotisation',
                'sens_rubrique'      => 'Retenu',
                'categorie_rubrique' => 'Personnel',
                'calcul_auto'        => 'CNSS_PAT',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('bulletin_calculer_models')
            ->whereIn('rubrique_id', DB::table('rubrique_models')->where('calcul_auto', 'CNSS_PAT')->pluck('id'))
            ->delete();
        DB::table('rubrique_models')->where('calcul_auto', 'CNSS_PAT')->delete();

        Schema::table('bulletin_calculer_models', function (Blueprint $table) {
            $table->dropColumn('montant_patronal');
        });
    }
};
