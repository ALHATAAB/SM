<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Certificats de scolarité (élèves) et certificats de travail (enseignants, personnel).
     * Une ligne = un certificat émis. Un duplicata remplace le précédent (« remplace »).
     */
    public function up(): void
    {
        if (Schema::hasTable('certificat_models')) {
            return;
        }

        Schema::create('certificat_models', function (Blueprint $table) {
            $table->id();
            $table->string('type', 15);                 // scolarite | travail
            $table->string('personne_type', 20);        // apprenant | enseignant | employer
            $table->unsignedBigInteger('personne_id');  // pre_inscription_id | enseignant_id | employer_id
            $table->string('numero')->unique();
            $table->unsignedBigInteger('annee_scolaire_id')->nullable();
            $table->date('date_emission');
            $table->date('date_debut')->nullable();     // travail : prise de fonction
            $table->date('date_fin')->nullable();       // travail : fin de fonction (vide = toujours en poste)
            $table->string('fonction')->nullable();     // travail : poste occupé
            $table->boolean('libre_engagement')->default(false);
            $table->string('signataire')->nullable();
            $table->string('qualite_signataire')->nullable();
            $table->string('statut', 15)->default('valide'); // valide | annule | remplace
            $table->unsignedBigInteger('duplicata_de')->nullable();
            $table->unsignedInteger('nombre_impressions')->default(0);
            $table->timestamp('date_impression')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['type', 'personne_type', 'personne_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificat_models');
    }
};
