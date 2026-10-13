<?php

namespace App\Models\Comptabilite;

use App\Models\Auth\User;
use App\Models\Comptabilite\BanqueModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementBanqueModel extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    protected $table = 'mouvement_banque_models';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function banque()
    {
        return $this->belongsTo(BanqueModel::class,'banque_id');
    }
}
