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
        Schema::create('caisse_models', function (Blueprint $table) {
            $table->id();
            $table->string('caisse');
            $table->double('repport')->default(0);
            $table->double('montant_min')->default(0);
            $table->double('montant_max')->default(0);
            $table->double('solde')->default(0);
            $table->date('date_comptable')->nullable();
            $table->enum('etat', ['ouverte', 'fermer'])->default('fermer'); 
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisse_models');
    }
};
