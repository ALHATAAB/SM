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
        Schema::create('taux_charge_familliale_models', function (Blueprint $table) {
            $table->id();
            $table->integer('nombre_de_charge');
            $table->decimal('taux_charge',10,2    );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taux_charge_familliale_models');
    }
};
