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
        Schema::create('historique_paiement_enseignant_models', function (Blueprint $table) {
            $table->id();
             $table->foreignId('annee_scolaire_id')->constrained();
            $table->foreignId('caisse_id')->constrained()->default(0);
            $table->foreignId('banque_id')->constrained()->default(0);
            $table->foreignId('mois_id')->constrained();
            $table->foreignId('annee_id')->constrained();
            $table->foreignId('user_id')->constrained()->references('id')->on('user');
            $table->string('matricule');
            $table->foreignId('enseignant_id')->constrained();
            $table->double('salaire_payer');
            $table->string('mode_paiement');
            $table->string('coordonnee_paiement')->nullable();
            $table->date('date_comptable');
            $table->integer('etat')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_paiement_enseignant_models');
    }
};
