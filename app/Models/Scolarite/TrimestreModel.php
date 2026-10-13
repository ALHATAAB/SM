<?php

namespace App\Models\Scolarite;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrimestreModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'trimestre_models';
    
}
