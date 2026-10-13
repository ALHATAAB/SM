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
        Schema::create('note_apprenant_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_etude_id')->constrained()->onDelete('cascade');
            $table->foreignId('matiere_etude_id')->constrained()->onDelete('cascade');
            $table->foreignId('trimestre_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['classe', 'composition']);
            $table->float('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_apprenant_models');
    }
};
