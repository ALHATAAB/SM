<?php

namespace App\Models\PaieRH;

use Illuminate\Database\Eloquent\Model;

class ConstantePaieModel extends Model
{
    protected $guarded = [];

    protected $table = 'constante_paie_models';

    public const CATEGORIES = [
        'tranche' => 'Tranche (borne min / max)',
        'calcul'  => 'Calcul (taux, coefficient, montant fixe)',
        'test'    => 'Test (seuil utilisé dans une condition)',
        'autre'   => 'Autre',
    ];
}
