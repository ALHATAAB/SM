<?php

namespace App\Models\PaieRH;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoisPaieModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'mois_paie_models';
}
