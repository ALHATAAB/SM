<?php

namespace App\Models\Scolarite;

use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisScolaireModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'frais_scolaire_models';

    public function niveauEtude()
    {
        return $this->belongsTo(NiveauEtudeModel::class);
    }

    public function filiereEtude()
    {
        return $this->belongsTo(FiliereEtudeModel::class);
    }
}
