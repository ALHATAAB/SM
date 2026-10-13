<?php

namespace App\Models\PaieRH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotisationModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'cotisation_models';

    protected $casts = ['actif' => 'boolean'];
}
