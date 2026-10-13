<?php

use App\Models\PaieRH\MoisPaieModel;
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
        Schema::create('mois_paie_models', function (Blueprint $table) {
            $table->id();
            $table->string('mois');
            $table->timestamps();
        });


        $mois_paies = [
            'JANVIER',
            'FEVRIER',
            'MARS',
            'AVRIL',
            'MAI',
            'JUIN',
            'JUILLET',
            'AOUT',
            'SEPTEMBRE',
            'OCTOBRE',
            'NOVEMBRE',
            'DECEMBRE',
        ];
        
        foreach ($mois_paies as $mois_paie) {
            MoisPaieModel::create([
                'mois' => $mois_paie,
            ]);
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mois_paie_models');
    }
};
