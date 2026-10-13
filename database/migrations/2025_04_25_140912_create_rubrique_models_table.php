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
        Schema::create('rubrique_models', function (Blueprint $table) {
            $table->id();
            $table->integer('code_rubrique');
            $table->string('rubrique');
            $table->enum('type_rubrique',[
                'Brute I',
                'Brute NI',
                'Cotisation',
                'Non soumise',
            ])->default('Brute I');
            $table->enum('sens_rubrique', ['Gain', 'Retenu'])->default('Gain');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrique_models');
    }
};
