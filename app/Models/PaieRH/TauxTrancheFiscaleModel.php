<?php

namespace App\Models\PaieRH;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TauxTrancheFiscaleModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'taux_tranche_fiscale_models';
}
