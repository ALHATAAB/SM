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
        Schema::create('historique_paiement_salaire_models', function (Blueprint $table) {
            $table->id();
             $table->foreignId('annee_scolaire_id')->constrained('annee_scolaire_models');
            $table->foreignId('caisse_id')->constrained('caisse_models')->default(0);
            $table->foreignId('banque_id')->constrained('banque_models')->default(0);
            $table->foreignId('mois_id')->constrained('mois_paie_models');
            $table->foreignId('annee_id')->constrained('annee_scolaire_models');
            $table->foreignId('user_id')->constrained('users');
            $table->string('matricule');
            $table->foreignId('employer_id')->constrained('employer_models');
            $table->double('salaire_payer');
            $table->string('mode_paiement');
            $table->string('coordonnee_paiement')->nullable();
            $table->date('date_comptable');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_paiement_salaire_models');
    }
};
