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
        Schema::create('document_inscription_models', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->nullable();
            $table->foreignId('pre_inscription_id')->constrained();
            $table->string('photo_identite')->nullable();
            $table->string('acte_naissance')->nullable();
            $table->string('nationnalite')->nullable();
            $table->string('dernier_diplome')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_inscription_models');
    }
};
