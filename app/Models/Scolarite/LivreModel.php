<?php

namespace App\Models\Scolarite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivreModel extends Model
{
     use HasFactory;

    protected $guarded = [] ;

    protected $table = 'livre_models';

    public function ligneAchats()
    {
        return $this->hasMany(LigneAchatModel::class,'livre_id');
    }

    public function ligneVentes()
    {
        return $this->hasMany(LigneVenteModel::class,'livre_id');
    }
    
}
