<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigDateComptableModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'config_date_comptable_models';
}
