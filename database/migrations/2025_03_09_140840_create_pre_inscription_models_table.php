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
        Schema::create('pre_inscription_models', function (Blueprint $table) {
            $table->id();
            $table->string('matricule');
            $table->string('nom');
            $table->string('prenom');
            $table->string('sexe');
            $table->string('groupe');
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('prenom_parent')->nullable();
            $table->string('telephone_parent')->nullable();
            $table->string('adresse_parent')->nullable();
            $table->string('nationalite')->nullable();
            $table->string('pays')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_inscription_models');
    }
};
