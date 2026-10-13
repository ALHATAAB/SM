<?php

use App\Models\Scolarite\CoefficientMatiereModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Correction des notes déjà saisies : l'écran « Note apprenant par classe » enregistrait
 * l'identifiant de la ligne de coefficient dans matiere_etude_id (au lieu de l'identifiant
 * de la matière), si bien que le bulletin ne retrouvait pas les notes.
 *
 * À exécuter une seule fois, AVANT de ressaisir des notes avec la version corrigée.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('note_apprenant_models')->orderBy('id')->get()->each(function ($note) {
                $coef = CoefficientMatiereModel::where('id', $note->matiere_etude_id)
                    ->where('classe_etude_id', $note->classe_etude_id)
                    ->first();

                // Pas de ligne de coefficient correspondante : note déjà correcte, on n'y touche pas.
                if (!$coef || (int) $coef->matiere_etude_id === (int) $note->matiere_etude_id) {
                    return;
                }

                $existe = DB::table('note_apprenant_models')
                    ->where('apprenant_id', $note->apprenant_id)
                    ->where('classe_etude_id', $note->classe_etude_id)
                    ->where('matiere_etude_id', $coef->matiere_etude_id)
                    ->where('trimestre_id', $note->trimestre_id)
                    ->where('type', $note->type)
                    ->exists();

                if ($existe) {
                    // Une note correcte existe déjà pour cette case : on garde celle-là.
                    DB::table('note_apprenant_models')->where('id', $note->id)->delete();
                } else {
                    DB::table('note_apprenant_models')->where('id', $note->id)
                        ->update(['matiere_etude_id' => $coef->matiere_etude_id]);
                }
            });
        });
    }

    public function down(): void
    {
        // Irréversible : l'ancien identifiant (ligne de coefficient) n'est pas conservé.
    }
};
