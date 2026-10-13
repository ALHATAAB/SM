<?php

use App\Models\Config\ModePaiementModel;
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
        Schema::create('mode_paiement_models', function (Blueprint $table) {
            $table->id();
            $table->string('mode_paiement');
            $table->timestamps();
        });

        $mode_paiements = [
            'ESPECE',
            'DEPOT',
            'TRANSFERT',
            'CHEQUE',
        ];
        
        foreach ($mode_paiements as $mode_paiement) {
            ModePaiementModel::create([
                'mode_paiement' => $mode_paiement,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mode_paiement_models');
    }
};
