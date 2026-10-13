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
        Schema::create('inscription_models', function (Blueprint $table) {
            $table->id();
            $table->string('matricule');
            $table->foreignId('pre_inscription_id')->constrained();
            $table->foreignId('niveau_etude_id')->constrained();
            $table->foreignId('filiere_etude_id')->constrained();
            $table->foreignId('classe_etude_id')->constrained();
            $table->foreignId('annee_scolaire_id')->constrained();
            $table->double('frais_inscription');
            $table->double('frais_scolarite');
            $table->double('payer')->nullable();
            $table->double('rester')->nullable();
            $table->double('remise')->nullable();
            $table->integer('etat')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscription_models');
    }
};
