<?php

namespace App\Models\Scolarite;

use App\Models\Auth\User;
use App\Models\Comptabilite\BanqueModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\EnseignantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriquePaiementEnseignantModel extends Model
{
   use HasFactory;
    protected $guarded = [] ;

    protected $table = 'historique_paiement_enseignant_models';

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
    
    public function enseignant()
    {
        return $this->belongsTo(EnseignantModel::class,'enseignant_id');
    }

      public function mois()
    {
        return $this->belongsTo(MoisPaieModel::class,'mois_id');
    }

     public function annee()
    {
        return $this->belongsTo(AnneeScolaireModel::class,'annee_id');
    }

}
