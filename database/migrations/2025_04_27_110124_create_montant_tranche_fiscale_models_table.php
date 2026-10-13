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
        Schema::create('montant_tranche_fiscale_models', function (Blueprint $table) {
            $table->id();
            $table->decimal('tranche_min', 10, 2);
            $table->decimal('tranche_max', 10, 2);
            $table->decimal('valeur_fiscale', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('montant_tranche_fiscale_models');
    }
};
