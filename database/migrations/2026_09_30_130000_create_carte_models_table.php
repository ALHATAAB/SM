<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cartes scolaires (élèves) et cartes professionnelles (enseignants, personnel).
     * Une ligne = une carte émise. Une nouvelle carte (duplicata) remplace la précédente :
     * l'ancienne passe à « remplacee » (ou reste « perdue » / « annulee »).
     */
    public function up(): void
    {
        Schema::create('carte_models', function (Blueprint $table) {
            $table->id();
            $table->string('categorie', 20);          // scolaire | professionnelle
            $table->string('personne_type', 20);      // apprenant | enseignant | employer
            $table->unsignedBigInteger('personne_id'); // pre_inscription_id | enseignant_id | employer_id
            $table->string('numero')->unique();
            $table->unsignedBigInteger('annee_scolaire_id')->nullable();
            $table->date('date_emission');
            $table->date('date_expiration');
            $table->string('statut', 15)->default('active'); // active | perdue | annulee | remplacee
            $table->unsignedBigInteger('duplicata_de')->nullable();
            $table->unsignedInteger('nombre_impressions')->default(0);
            $table->timestamp('date_impression')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['categorie', 'personne_type', 'personne_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carte_models');
    }
};
