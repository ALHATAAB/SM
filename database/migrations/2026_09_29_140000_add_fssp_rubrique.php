<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la rubrique FSSP (retenue automatique = % du salaire net, cf. config/paie.php).
     * Si la migration précédente avait créé calcul_auto en ENUM (MySQL), la colonne est
     * d'abord convertie en texte pour accepter la valeur « FSSP ».
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE rubrique_models MODIFY calcul_auto VARCHAR(20) NULL');
        }

        if (!DB::table('rubrique_models')->where('calcul_auto', 'FSSP')->exists()) {
            $now = now();
            DB::table('rubrique_models')->insert([
                'code_rubrique'      => ((int) DB::table('rubrique_models')->max('code_rubrique')) + 1,
                'rubrique'           => 'FSSP',
                'type_rubrique'      => 'Cotisation',
                'sens_rubrique'      => 'Retenu',
                'categorie_rubrique' => 'Personnel',
                'calcul_auto'        => 'FSSP',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('rubrique_models')->where('calcul_auto', 'FSSP')->delete();
    }
};
