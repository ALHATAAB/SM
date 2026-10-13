<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Retenues optionnelles sur le paiement d'un enseignant :
 *  - montant_brut     : total de la fiche de paie avant retenues
 *  - retenue_its      : retenue ITS appliquée (0 si non cochée)
 *  - autres_retenues  : autres retenues / avance (0 si aucune)
 * salaire_payer reste le net effectivement versé (brut - ITS - autres retenues).
 * Sans effet si les colonnes existent déjà.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('historique_paiement_enseignant_models')) {
            return;
        }

        Schema::table('historique_paiement_enseignant_models', function (Blueprint $table) {
            if (!Schema::hasColumn('historique_paiement_enseignant_models', 'montant_brut')) {
                $table->double('montant_brut')->nullable();
            }
            if (!Schema::hasColumn('historique_paiement_enseignant_models', 'retenue_its')) {
                $table->double('retenue_its')->default(0);
            }
            if (!Schema::hasColumn('historique_paiement_enseignant_models', 'autres_retenues')) {
                $table->double('autres_retenues')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('historique_paiement_enseignant_models', function (Blueprint $table) {
            foreach (['montant_brut', 'retenue_its', 'autres_retenues'] as $c) {
                if (Schema::hasColumn('historique_paiement_enseignant_models', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
