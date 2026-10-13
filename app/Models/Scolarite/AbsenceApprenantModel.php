<?php

namespace App\Models\Scolarite;

use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\SalleEtudeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceApprenantModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'absence_apprenant_models';

    public function Inscription()
    {
        return $this->belongsTo(InscriptionModel::class,'inscription_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(EnseignantModel::class,);
    }
    
    public function matiere() {
        return $this->belongsTo(MatiereEtudeModel::class,'matiere_etude_id');
    }

    public function classe() {
        return $this->belongsTo(ClasseEtudeModel::class,'classe_etude_id');
    }

    public function salle() {
        return $this->belongsTo(SalleEtudeModel::class,'salle_etude_id');
    }

    public function seance() {
        return $this->belongsTo(SeanceModel::class);
    }
}
