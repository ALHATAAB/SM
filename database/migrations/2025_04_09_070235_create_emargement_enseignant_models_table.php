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

        Schema::create('emargement_enseignant_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained(); // Référence à un enseignant
            $table->foreignId('matiere_etude_id')->constrained();
            $table->foreignId('classe_etude_id')->constrained();
            $table->foreignId('salle_etude_id')->constrained();
            $table->foreignId('annee_scolaire_id')->constrained();
            $table->foreignId('seance_id')->nullable()->constrained('seance_models'); // Référence à un cours (facultatif)
            $table->double('nombre_heure'); // heure de presence
            $table->date('date'); // Date de présence
            $table->time('heure_arrivee')->nullable(); // Heure d’arrivée (facultatif)
            $table->time('heure_depart')->nullable(); // Heure de départ (facultatif)
            $table->enum('status', ['present', 'absent'])->default('absent'); // Statut
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emargement_enseignant_models');
    }
};
