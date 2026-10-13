<?php

namespace App\Models\Scolarite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchatModel extends Model
{
     use HasFactory;

    protected $guarded = [] ;

    protected $table = 'achat_models';

   public function ligneAchats()
    {
        return $this->hasMany(LigneAchatModel::class, 'achat_id');
    }
}
