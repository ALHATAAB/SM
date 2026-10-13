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
        Schema::create('trimestre_models', function (Blueprint $table) {
            $table->id();
            $table->string('trimestre');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('annee_scolaire_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trimestre_models');
    }
};
