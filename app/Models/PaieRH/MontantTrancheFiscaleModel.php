<?php

namespace App\Models\PaieRH;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MontantTrancheFiscaleModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'montant_tranche_fiscale_models';
}
