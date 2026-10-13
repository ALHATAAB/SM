<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenceModel extends Model
{
    protected $table = 'licence';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'DESIGNATION',
        'DATE_DEBUT',
        'DATE_FIN',
        'INSCRIT',
        'UTILISATEUR',
        'PRIX_VENTES',
        'CLE_ACTIVATION',
        'STATUS',
        'annee_scolaire_id',
    ];

    public function annee()
    {
        return $this->belongsTo(\App\Models\Scolarite\AnneeScolaireModel::class, 'annee_scolaire_id');
    }

    public function scopeActive($query)
    {
        return $query->where('STATUS', 1);
    }
}
