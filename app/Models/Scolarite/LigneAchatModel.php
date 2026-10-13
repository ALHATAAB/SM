<?php

namespace App\Models\Scolarite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneAchatModel extends Model
{

    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'ligne_achat_models';

   // Une ligne d'achat appartient à un achat
    public function achat_models()
    {
        return $this->belongsTo(AchatModel::class, 'achat_id');
    }

    // Une ligne d'achat appartient à un livre
    public function livre()
    {
        return $this->belongsTo(LivreModel::class, 'livre_id');
    }
}
