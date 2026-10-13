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
        Schema::create('livre_models', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('auteur')->nullable();
            $table->string('isbn')->nullable();
            $table->double('prix_achat');
            $table->double('prix_vente');
            $table->integer('stock_initial')->default(0);
            $table->integer('stock_actuel')->default(0);
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livre_models');
    }
};
