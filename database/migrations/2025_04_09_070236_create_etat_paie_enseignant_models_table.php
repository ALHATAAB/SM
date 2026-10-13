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
        Schema::create('etat_paie_enseignant_models', function (Blueprint $table) {
            $table->id();
             $table->foreignId('enseignant_id')->constrained(); // Référence à un enseignant
            $table->foreignId('mois_id')->constrained(); // Mois
            $table->integer('annee'); // Année
            $table->decimal('montant_base', 10, 2); // Montant de base
            $table->decimal('heures_sup', 10, 0)->default(0); // Heures supplémentaires
            $table->decimal('montant_heures_sup', 10, 2)->default(0); // Montant des heures supplémentaires
            $table->decimal('total_paye', 10, 2); // Total payé
            $table->enum('statut', ['payé', 'en_attente'])->default('en_attente'); // Statut du paiement
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etat_paie_enseignant_models');
    }
};
