<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ANPE part patronale = ligne du bulletin à part (colonne `montant_patronal`), comme la CNSS
     * patronale : charge de l'employeur, jamais comptée dans total_gain / total_retenu / salaire_net.
     *
     * - cotisation_models.organisme : « CNSS » (défaut) ou « ANPE ». Les branches ANPE sortent du
     *   total de la CNSS patronale et alimentent la rubrique « ANPE_PAT ».
     * - rubrique_models : rubrique « ANPE (part patronale) », calcul_auto = ANPE_PAT, générée
     *   automatiquement avec la CNSS lors du calcul de l'état de paie (jamais ajoutée à la main).
     * - fiche_paie_models.anpe_patronal : total ANPE patronale de la fiche.
     */
    public function up(): void
    {
        Schema::table('cotisation_models', function (Blueprint $table) {
            $table->string('organisme', 10)->default('CNSS')->after('intitule');
        });

        DB::table('cotisation_models')->where('intitule', 'like', 'ANPE%')->update(['organisme' => 'ANPE']);

        Schema::table('fiche_paie_models', function (Blueprint $table) {
            $table->decimal('anpe_patronal', 12, 2)->default(0)->after('cnss_patronal');
        });

        if (!DB::table('rubrique_models')->where('calcul_auto', 'ANPE_PAT')->exists()) {
            $now = now();
            DB::table('rubrique_models')->insert([
                'code_rubrique'      => ((int) DB::table('rubrique_models')->max('code_rubrique')) + 1,
                'rubrique'           => 'ANPE (part patronale)',
                'type_rubrique'      => 'Cotisation',
                'sens_rubrique'      => 'Retenu',
                'categorie_rubrique' => 'Personnel',
                'calcul_auto'        => 'ANPE_PAT',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('bulletin_calculer_models')
            ->whereIn('rubrique_id', DB::table('rubrique_models')->where('calcul_auto', 'ANPE_PAT')->pluck('id'))
            ->delete();
        DB::table('rubrique_models')->where('calcul_auto', 'ANPE_PAT')->delete();

        Schema::table('fiche_paie_models', function (Blueprint $table) {
            $table->dropColumn('anpe_patronal');
        });

        Schema::table('cotisation_models', function (Blueprint $table) {
            $table->dropColumn('organisme');
        });
    }
};
