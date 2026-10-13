<?php

namespace App\Models\PaieRH;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TauxChargeFamillialeModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'taux_charge_familliale_models';
}
