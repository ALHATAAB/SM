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
        Schema::create('date_comptable_models', function (Blueprint $table) {
            $table->id();
            $table->date('date_comptable');
            $table->foreignId('annee_scolaire_id');
            $table->enum('statuts',['OUVERTE','FERMEE'])->default('FERMEE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('date_comptable_models');
    }
};
