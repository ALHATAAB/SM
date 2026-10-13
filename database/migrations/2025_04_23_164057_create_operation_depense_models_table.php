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
        Schema::create('operation_depense_models', function (Blueprint $table) {
            $table->id();
             $table->foreignId('annee_scolaire_id')->constrained();
            $table->foreignId('user_id')->constrained()->references('id')->on('user');
            $table->double('montant');
            $table->string('reference');
            $table->string('mode_paiement');
            $table->date('date_comptable');
            $table->integer('etat')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_depense_models');
    }
};
