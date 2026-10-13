<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chaque rubrique de paie précise désormais à quel type d'employé elle
     * s'applique : Personnel (administratif), Enseignant, ou Commun aux deux.
     * C'est cette valeur qui permettra, lors du calcul du bulletin, de ne
     * proposer que les rubriques pertinentes selon le type de l'employé.
     */
    public function up(): void
    {
        Schema::table('rubrique_models', function (Blueprint $table) {
            $table->enum('categorie_rubrique', ['Commun', 'Personnel', 'Enseignant'])
                ->default('Commun')
                ->after('type_rubrique');
        });
    }

    public function down(): void
    {
        Schema::table('rubrique_models', function (Blueprint $table) {
            $table->dropColumn('categorie_rubrique');
        });
    }
};
