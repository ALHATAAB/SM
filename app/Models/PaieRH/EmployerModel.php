<?php

namespace App\Models\PaieRH;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'employer_models';

}
