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
        Schema::create('frais_scolaire_models', function (Blueprint $table) {
            $table->id();
            $table->integer('niveau_etude_id')->nullable();
            $table->integer('filiere_etude_id')->nullable();
            $table->float('frais_inscription')->default(0);
            $table->float('frais_scolaire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frais_scolaire_models');
    }
};
