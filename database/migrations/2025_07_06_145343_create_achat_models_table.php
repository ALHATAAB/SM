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
        Schema::create('achat_models', function (Blueprint $table) {
            $table->id();
            $table->string('fournisseur')->nullable(); // nom du fournisseur
            $table->decimal('montant_total', 10, 2);
            $table->foreignId('user_id')->constrained()->references('id')->on('user');
            $table->enum('statuts',[
                'annuler',
                'valider',
            ])->default('valider');
            $table->date('date_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achat_models');
    }
};
