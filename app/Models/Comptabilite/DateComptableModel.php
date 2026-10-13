<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateComptableModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'date_comptable_models';
}
