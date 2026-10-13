<?php

namespace App\Models\Comptabilite;
use App\Models\Auth\User;
use App\Models\Comptabilite\CaisseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementCaisseModel extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    protected $table = 'mouvement_caisse_models';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function caisse()
    {
        return $this->belongsTo(CaisseModel::class,'caisse_id');
    }
}
