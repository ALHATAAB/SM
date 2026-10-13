<?php

namespace App\Models\Scolarite;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scolarite\AbsenceApprenantModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use App\Models\Scolarite\PreInscriptionModel;
use Illuminate\Database\Eloquent\Model;

class InscriptionModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'inscription_models';

    public function preInscription()
    {
        return $this->belongsTo(PreInscriptionModel::class,'pre_inscription_id');
    }

      public function absences()
    {
        return $this->hasMany(AbsenceApprenantModel::class);
    }
    
    public function niveau()
    {
        return $this->belongsTo(NiveauEtudeModel::class,'niveau_etude_id');
    }

    public function filiere()
    {
        return $this->belongsTo(FiliereEtudeModel::class,'filiere_etude_id');
    }

    public function classe()
    {
        return $this->belongsTo(ClasseEtudeModel::class,'classe_etude_id');
    }

    public function annee()
    {
        return $this->belongsTo(AnneeScolaireModel::class,'annee_scolaire_id');
    }
}
