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
        Schema::create('fiche_paie_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employer_models');
            $table->string('reference');
            $table->decimal('total_gain', 10, 2)->nullable();
            $table->decimal('total_retenu', 10, 2)->nullable();
            $table->decimal('salaire_net', 10, 2)->nullable();
            $table->enum('statuts',['EN COURS','PAIE'])->default('EN COURS');
            $table->foreignId('mois_id')->constrained('mois_paie_models');
            $table->foreignId('annee_id')->constrained('annee_scolaire_models');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_paie_models');
    }
};
