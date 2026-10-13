<?php

namespace App\Models\Programme;

use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\ClasseEtudeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploiDuTempsModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'emploi_du_temps_models';
    
    public function classe() 
    {
        return $this->belongsTo(ClasseEtudeModel::class);
    }

    public function seances() 
    {
        return $this->hasMany(SeanceModel::class);
    }
}
