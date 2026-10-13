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
        Schema::create('banque_models', function (Blueprint $table) {
            $table->id();
            $table->string('banque');
            $table->string('numero_compte');
            $table->double('solde')->default(0);
            $table->enum('etat', ['ouverte', 'fermer'])->default('ouverte'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banque_models');
    }
};
