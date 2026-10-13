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
        Schema::create('seance_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained();
            $table->foreignId('matiere_id')->constrained();
            $table->foreignId('classe_id')->constrained();
            $table->foreignId('salle_id')->constrained();
            $table->foreignId('emploi_du_temps_id')->constrained('emploi_du_temps_models');
            $table->string('jour');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seance_models');
    }
};
