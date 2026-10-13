<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('config_date_comptable_models')) {
            Schema::create('config_date_comptable_models', function (Blueprint $table) {
                $table->id();
                $table->enum('mode', ['MANUELLE', 'AUTOMATIQUE'])->default('MANUELLE');
                $table->timestamps();
            });

            // Par défaut : on garde le comportement actuel (manuel)
            DB::table('config_date_comptable_models')->insert([
                'id' => 1,
                'mode' => 'MANUELLE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('config_date_comptable_models');
    }
};
