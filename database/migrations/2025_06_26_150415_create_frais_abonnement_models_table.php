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
        Schema::create('frais_abonnement_models', function (Blueprint $table) {
            $table->id();
            $table->integer('type_abonnement_id')->nullable();
            $table->string('periode')->nullable();
            $table->float('nombre')->default(0);
            $table->float('frais_abonnement')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frais_abonnement_models');
    }
};
