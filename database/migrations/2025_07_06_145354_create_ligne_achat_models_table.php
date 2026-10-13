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
        Schema::create('ligne_achat_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achat_id')->constrained('achat_models')->onDelete('cascade');
            $table->foreignId('livre_id')->constrained('livre_models')->onDelete('restrict');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('prix_total', 10, 2);
            $table->date('date_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_achat_models');
    }
};
