<?php

namespace App\Models\Scolarite;

use App\Models\Auth\User;
use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Scolarite\InscriptionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriquePaiementScolariteModel extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    protected $table = 'historique_paiement_scolarite_models';

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

    public function inscription()
    {
        return $this->belongsTo(InscriptionModel::class,'inscription_id');
    }
}
