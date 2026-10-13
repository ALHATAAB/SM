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
        Schema::create('model_bulletin_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubrique_id')->constrained('rubrique_models');
            $table->foreignId('employer_id')->constrained('employer_models');
            $table->decimal('taux', 10, 2)->nullable();
            $table->decimal('nombre', 10, 2)->nullable();
            $table->decimal('base', 10, 2)->nullable();
            $table->decimal('montant_gain', 10, 2)->nullable();
            $table->decimal('montant_retenu', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_bulletin_models');
    }
};
