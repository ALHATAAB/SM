<?php

namespace App\Models\PaieRH;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeRubriqueModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'type_rubrique_models';
}
