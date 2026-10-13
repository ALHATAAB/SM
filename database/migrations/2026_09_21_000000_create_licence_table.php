<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('licence')) {
            Schema::create('licence', function (Blueprint $table) {
                $table->increments('ID');
                $table->string('DESIGNATION', 150)->nullable();
                $table->date('DATE_DEBUT')->nullable();
                $table->date('DATE_FIN')->nullable();
                $table->integer('INSCRIT')->nullable();
                $table->integer('UTILISATEUR')->nullable();
                $table->double('PRIX_VENTES')->nullable();
                $table->string('CLE_ACTIVATION', 225);
                $table->integer('STATUS')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('licence');
    }
};
