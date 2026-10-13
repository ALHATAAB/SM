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
        Schema::create('absence_apprenant_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_etude_id')->constrained()->onDelete('cascade');
            $table->foreignId('enseignant_id')->constrained()->onDelete('cascade');
            $table->foreignId('matiere_etude_id')->constrained()->onDelete('cascade');
            $table->foreignId('salle_etude_id')->constrained()->onDelete('cascade');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->date('date');
            $table->string('motif')->nullable();
            $table->boolean('justifiee')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_apprenant_models');
    }
};
