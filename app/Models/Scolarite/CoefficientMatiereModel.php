<?php

namespace App\Models\Scolarite;

use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\MatiereEtudeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoefficientMatiereModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'coefficient_matiere_models';

   

    public function MatiereEtude()
    {
        return $this->belongsTo(MatiereEtudeModel::class);
    }

    public function ClasseEtude()
    {
        return $this->belongsTo(ClasseEtudeModel::class);
    }
}
