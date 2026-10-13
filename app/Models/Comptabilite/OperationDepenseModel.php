<?php

namespace App\Models\Comptabilite;

use App\Models\Auth\User;
use App\Models\Scolarite\TypeAbonnementModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationDepenseModel extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    protected $table = 'operation_depense_models';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

     public function type_abonnement()
    {
        return $this->belongsTo(TypeAbonnementModel::class,'type_abonnement_id');
    }
    
}
