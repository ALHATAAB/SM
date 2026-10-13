<?php

namespace App\Models\Scolarite;

use App\Models\Auth\User;
use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Scolarite\PreInscriptionModel;
use App\Models\Scolarite\TypeAbonnementModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueAbonnementModel extends Model
{
     use HasFactory;
    protected $guarded = [] ;

    protected $table = 'historique_abonnement_models';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function caisse()
    {
        return $this->belongsTo(CaisseModel::class,'caisse_id');
    }

     public function banque()
    {
        return $this->belongsTo(BanqueModel::class,'banque_id');
    }

    public function preInscription()
    {
        return $this->belongsTo(PreInscriptionModel::class,'pre_inscription_id');
    }

    public function type_abonnement()
    {
        return $this->belongsTo(TypeAbonnementModel::class,'type_abonnement_id');
    }


}
