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
        Schema::create('enseignant_models', function (Blueprint $table) {
            $table->id();
            $table->string('matricule');
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('sexe');
            $table->string('adresse');
            $table->string('telephone')->nullable();
            $table->string('specialite')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('departement')->nullable();
            $table->string('matieres')->nullable();
            $table->string('grade')->nullable();
            $table->string('statut')->nullable();
            $table->integer('anciennete')->nullable();
            $table->enum('type_contrat', ['fixe', 'horaire', 'mixte']);
            $table->decimal('salaire_base', 10, 2)->default(0);
            $table->decimal('salaire_horaire', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignant_models');
    }
};
