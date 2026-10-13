<?php

namespace App\Models\PaieRH;

use App\Models\Auth\User;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Scolarite\AnneeScolaireModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriquePaiementSalaireModel extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    protected $table = 'historique_paiement_salaire_models';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function employer()
    {
        return $this->belongsTo(EmployerModel::class,'employer_id');
    }

    public function caisse()
    {
        return $this->belongsTo(CaisseModel::class,'caisse_id');
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
