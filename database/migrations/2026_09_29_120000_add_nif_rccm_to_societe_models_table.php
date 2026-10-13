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
        Schema::table('societe_models', function (Blueprint $table) {
            $table->string('nif')->nullable()->after('activite');
            $table->string('rccm')->nullable()->after('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('societe_models', function (Blueprint $table) {
            $table->dropColumn(['nif', 'rccm']);
        });
    }
};
