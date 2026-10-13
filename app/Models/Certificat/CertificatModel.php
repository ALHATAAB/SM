<?php

namespace App\Models\Certificat;

use Illuminate\Database\Eloquent\Model;

class CertificatModel extends Model
{
    protected $guarded = [];

    protected $table = 'certificat_models';

    protected $casts = [
        'date_emission'    => 'date',
        'date_debut'       => 'date',
        'date_fin'         => 'date',
        'date_impression'  => 'datetime',
        'libre_engagement' => 'boolean',
    ];

    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    public function estDuplicata(): bool
    {
        return $this->duplicata_de !== null;
    }
}
