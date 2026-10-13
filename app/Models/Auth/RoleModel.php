<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleModel extends Model
{
    use HasFactory;

    protected $fillable = ["nom"] ;

    protected $table = 'role_models';
}
