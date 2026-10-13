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
        Schema::create('abonnement_models', function (Blueprint $table) {
            $table->id();
            $table->string('matricule');
            $table->foreignId('pre_inscription_id')->constrained();
            $table->foreignId('type_abonnement_id')->constrained();
            $table->string('periode');
            $table->integer('nombre');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('frais_abonnement', 10, 2);
            $table->string('statut')->default('payé');
            $table->foreignId('annee_scolaire_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnement_models');
    }
};
