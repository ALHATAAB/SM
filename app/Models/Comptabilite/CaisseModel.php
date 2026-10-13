<?php

namespace App\Models\Comptabilite;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaisseModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'caisse_models';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
}
