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
        Schema::create('employer_models', function (Blueprint $table) {
            $table->id();  
            $table->string('matricule');
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('sexe');
            $table->string('adresse');
            $table->string('telephone');
            $table->string('email')->unique()->nullable();
            $table->string('poste');
            $table->string('charge_familliale')->nullable();
            $table->string('numero_cnss')->nullable();
            $table->decimal('salaire', 10, 2)->nullable();
            $table->date('date_embauche');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_models');
    }
};
