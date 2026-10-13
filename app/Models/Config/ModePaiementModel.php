<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModePaiementModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'mode_paiement_models';
    
}
