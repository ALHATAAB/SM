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
        Schema::create('mouvement_banque_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banque_id')->constrained();
            $table->foreignId('user_id')->constrained()->references('id')->on('user');;
            $table->text('description');
            $table->double('entree')->default(0);
            $table->double('sortie')->default(0);
            $table->double('solde');
            $table->date('date_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_banque_models');
    }
};
