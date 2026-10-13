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
        Schema::create('coefficient_matiere_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_etude_id')->constrained();
            $table->foreignId('matiere_etude_id')->constrained();
            $table->float('coefficient')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coefficient_matiere_models');
    }
};
