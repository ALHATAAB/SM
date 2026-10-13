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
        Schema::create('vente_models', function (Blueprint $table) {
            $table->id();
            $table->string('client')->nullable(); // nom du fournisseur
            $table->decimal('montant_total', 10, 2);
            $table->foreignId('user_id')->constrained()->references('id')->on('user');
            $table->foreignId('caisse_id')->constrained()->default(0);
            $table->foreignId('banque_id')->constrained()->default(0);
            $table->string('mode_paiement')->nullable();
            $table->string('coordonnee_paiement')->nullable();
            $table->enum('statuts',[
                'annuler',
                'valider',
            ])->default('valider');
            $table->date('date_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vente_models');
    }
};
