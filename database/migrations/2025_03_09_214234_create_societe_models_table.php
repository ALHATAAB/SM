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
        Schema::create('societe_models', function (Blueprint $table) {
            $table->id();
            $table->string('raison_sociale');
            $table->string('activite');
            $table->string('forme_juridique')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('adresse')->nullable();
            $table->string('complement')->nullable();
            $table->string('site_web')->nullable();
            $table->string('logo')->nullable();
            $table->string('Id_client')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_models');
    }
};
