<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('annee_scolaire_models', function (Blueprint $table) {
            $table->id();
            $table->year('annee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('etat', ['actif', 'inactif'])->default('inactif'); // Définition de ENUM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annee_scolaire_models');
    }
};
