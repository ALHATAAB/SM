<?php

namespace App\Models\Scolarite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbonnementModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'abonnement_models';

    public function preInscription()
    {
        return $this->belongsTo(PreInscriptionModel::class,'pre_inscription_id');
    }

    public function type_abonnement()
    {
        return $this->belongsTo(TypeAbonnementModel::class,'type_abonnement_id');
    }


    public function annee()
    {
        return $this->belongsTo(AnneeScolaireModel::class,'annee_scolaire_id');
    }
}
