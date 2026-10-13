<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * CNSS, ITS et FSSP ne s'appliquent désormais qu'aux employés dont le bulletin modèle
     * contient ces rubriques (ajout depuis la fiche employé, montant calculé automatiquement).
     *
     * Pour ne rien changer à la paie des employés déjà paramétrés (jusqu'ici les trois
     * rubriques leur étaient appliquées d'office), on les leur rattache une fois.
     * Les montants sont calculés à l'ouverture de la fiche et à la génération de l'état de paie.
     */
    public function up(): void
    {
        $rubriques = DB::table('rubrique_models')->whereNotNull('calcul_auto')->pluck('id');
        $employes  = DB::table('model_bulletin_models')->distinct()->pluck('employer_id');
        $now       = now();

        foreach ($employes as $employerId) {
            foreach ($rubriques as $rubriqueId) {
                $existe = DB::table('model_bulletin_models')
                    ->where('employer_id', $employerId)
                    ->where('rubrique_id', $rubriqueId)
                    ->exists();

                if (!$existe) {
                    DB::table('model_bulletin_models')->insert([
                        'employer_id'    => $employerId,
                        'rubrique_id'    => $rubriqueId,
                        'montant_retenu' => 0,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $rubriques = DB::table('rubrique_models')->whereNotNull('calcul_auto')->pluck('id');
        DB::table('model_bulletin_models')->whereIn('rubrique_id', $rubriques)->delete();
    }
};
