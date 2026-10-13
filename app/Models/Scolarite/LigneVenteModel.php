<?php

namespace App\Models\Scolarite;

use App\Models\Scolarite\LivreModel;
use App\Models\Scolarite\VenteModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneVenteModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'ligne_vente_models';

    // Une ligne de vente appartient à une vente
    public function vente_models()
    {
        return $this->belongsTo(VenteModel::class, 'vente_id');
    }

    // Une ligne de vente appartient à un livre
    public function livre()
    {
        return $this->belongsTo(LivreModel::class, 'livre_id');
    }
}
