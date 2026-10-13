<?php

namespace App\Models\Scolarite;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\Scolarite\EnseignantModel;
use Illuminate\Database\Eloquent\Model;

class EtatPaieEnseignantModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'etat_paie_enseignant_models';

    public function enseignant()
    {
        return $this->belongsTo(EnseignantModel::class);
    }

    public function mois()
    {
        return $this->belongsTo(MoisPaieModel::class,);
    }
}
