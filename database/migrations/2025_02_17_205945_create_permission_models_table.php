<?php

use App\Models\Auth\PermissionModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_models', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->string('slug')->nullable();
            $table->integer('groupBy')->default(0);
            $table->timestamps();
        });

        $permission_models=[
            [  'Parametre','Parametre','0'],
            [  'Afficher','Afficher','1'],
            [  'Ajouter','Ajouter','1'],
            [  'Valider','Valider','1'],
            [  'Modifier','Modifier','1'],
            [  'Supprimer','Supprimer','1'],
            [  'Consulter','Consulter','1'],
            [  'Configuration','Configuration','2'],
            [  'Comptabilite','Comptabilite','3'],
            [  'Tresorier','Tresorier','4'],
            [  'RH','RH','5'],
            [  'Scolarite','Scolarite','6'],
            [  'Programme','Programme','7'],
            [  'Enseignant','Enseignant','8'],
            [  'Bibliotheque','Bibliotheque','9'],
            [  'Employer','Employer','10'],
            [  'Abonnement','Abonnement','11'],
        ];

        foreach($permission_models as $permis){
            PermissionModel::create([
                'nom'=>$permis[0],
                'slug'=>$permis[1],
                'groupBy'=>$permis[2],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_models');
    }
};
