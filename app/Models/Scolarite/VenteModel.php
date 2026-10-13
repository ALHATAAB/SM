<?php

namespace App\Models\Scolarite;

use App\Models\Scolarite\LigneVenteModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenteModel extends Model
{
     use HasFactory;

    protected $guarded = [] ;

    protected $table = 'vente_models';

    public function ligneVentes()
    {
        return $this->hasMany(LigneVenteModel::class, 'vente_id');
    }
}
