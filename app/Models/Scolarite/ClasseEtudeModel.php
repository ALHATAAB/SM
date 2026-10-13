<?php

namespace App\Models\Scolarite;

use App\Models\Programme\EmploiDuTempsModel;
use App\Models\Programme\SeanceModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasseEtudeModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'classe_etude_models';

    public function emploisDuTemps() {
        return $this->hasMany(EmploiDuTempsModel::class);
    }

    public function seances() {
        return $this->hasMany(SeanceModel::class);
    }
}
