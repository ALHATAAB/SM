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
        Schema::create('appro_caisse_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->references('id')->on('user');
            $table->decimal('montant', 10, 2);
            $table->string('reference');
            $table->foreignId('caisse_id')->constrained();
            $table->foreignId('caisse_destination_id')->constrained();
            $table->datetime('date_validation')->nullable(); 
            $table->enum('statuts',[
                'NON AUTORISER',
                'AUTORISER',
                'REJETER',
            ])->default('NON AUTORISER');
            $table->date('date_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appro_caisse_models');
    }
};
