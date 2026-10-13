<?php

namespace App\Models\Scolarite;

use App\Models\Auth\User;
use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use App\Models\Scolarite\PreInscriptionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueInscriptionModel extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    protected $table = 'historique_inscription_models';

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

    public function niveau()
    {
        return $this->belongsTo(NiveauEtudeModel::class,'niveau_etude_id');
    }

    public function filiere()
    {
        return $this->belongsTo(FiliereEtudeModel::class,'filiere_etude_id');
    }

    public function classe()
    {
        return $this->belongsTo(ClasseEtudeModel::class,'classe_etude_id');
    }
}
