<?php

namespace App\Models\Carte;

use Illuminate\Database\Eloquent\Model;

class CarteModel extends Model
{
    protected $guarded = [];

    protected $table = 'carte_models';

    protected $casts = [
        'date_emission'    => 'date',
        'date_expiration'  => 'date',
        'date_impression'  => 'datetime',
    ];

    /** Active ET non expirée. */
    public function estValide(): bool
    {
        return $this->statut === 'active' && !$this->date_expiration->isPast();
    }

    /** Statut affiché : une carte active dont la date est dépassée est « expiree ». */
    public function statutAffiche(): string
    {
        return ($this->statut === 'active' && $this->date_expiration->endOfDay()->isPast()) ? 'expiree' : $this->statut;
    }
}
