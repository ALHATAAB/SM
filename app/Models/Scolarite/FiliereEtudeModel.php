<?php

namespace App\Models\Scolarite;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Database\Eloquent\Model;

class FiliereEtudeModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'filiere_etude_models';

    public function niveau()
    {
        return $this->belongsTo(NiveauEtudeModel::class,'niveau_etude_id');
    }
}
