<?php

namespace App\Models\Scolarite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisAbonnementModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'frais_abonnement_models';

    public function type_abonnement()
    {
        return $this->belongsTo(TypeAbonnementModel::class,'type_abonnement_id');
    }
}
