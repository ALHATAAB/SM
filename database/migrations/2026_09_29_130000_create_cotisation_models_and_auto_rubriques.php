<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - cotisation_models : barème des cotisations sociales (CNSS), une ligne par branche.
     * - rubrique_models.calcul_auto : marque les rubriques dont le montant est calculé
     *   automatiquement lors du calcul de l'état de paie du personnel (CNSS, ITS).
     *
     * Les taux CNSS insérés ci-dessous sont les taux publiés par le CLEISS pour le Niger
     * (au 1er janvier 2024). Ils sont modifiables dans Paramètre paie RH et doivent être
     * confirmés auprès de la CNSS avant la mise en production.
     */
    public function up(): void
    {
        Schema::create('cotisation_models', function (Blueprint $table) {
            $table->id();
            $table->string('intitule');
            $table->decimal('taux_salarie', 5, 2)->default(0);
            $table->decimal('taux_patronal', 5, 2)->default(0);
            $table->decimal('plafond_mensuel', 12, 2)->default(0); // 0 = sans plafond
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('cotisation_models')->insert([
            ['intitule' => 'Vieillesse - invalidité - survivants', 'taux_salarie' => 5.25, 'taux_patronal' => 6.25, 'plafond_mensuel' => 500000, 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['intitule' => 'Prestations familiales et maternité', 'taux_salarie' => 0, 'taux_patronal' => 8.40, 'plafond_mensuel' => 500000, 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['intitule' => 'Accidents du travail - maladies professionnelles', 'taux_salarie' => 0, 'taux_patronal' => 1.75, 'plafond_mensuel' => 500000, 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['intitule' => 'ANPE', 'taux_salarie' => 0, 'taux_patronal' => 1.00, 'plafond_mensuel' => 500000, 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('rubrique_models', function (Blueprint $table) {
            $table->string('calcul_auto', 20)->nullable()->after('categorie_rubrique'); // CNSS, ITS, FSSP
        });

        $code = (int) DB::table('rubrique_models')->max('code_rubrique');
        foreach ([
            'CNSS' => 'CNSS (part salariale)',
            'ITS'  => 'Impôt sur les traitements et salaires (ITS)',
        ] as $auto => $libelle) {
            DB::table('rubrique_models')->insert([
                'code_rubrique'      => ++$code,
                'rubrique'           => $libelle,
                'type_rubrique'      => 'Cotisation',
                'sens_rubrique'      => 'Retenu',
                'categorie_rubrique' => 'Personnel',
                'calcul_auto'        => $auto,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('rubrique_models')->whereNotNull('calcul_auto')->delete();

        Schema::table('rubrique_models', function (Blueprint $table) {
            $table->dropColumn('calcul_auto');
        });

        Schema::dropIfExists('cotisation_models');
    }
};
