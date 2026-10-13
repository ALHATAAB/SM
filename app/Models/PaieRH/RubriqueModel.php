<?php

namespace App\Models\PaieRH;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubriqueModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'rubrique_models';

    /** Rubrique dont le montant est calculé automatiquement (CNSS ou ITS) : non saisissable à la main. */
    public function estAuto(): bool
    {
        return !empty($this->calcul_auto);
    }

    /** Rubrique automatique pilotée par une formule (base*taux, base*taux*nombre...), hors barèmes CNSS/ITS/FSSP. */
    public function estFormule(): bool
    {
        return empty($this->calcul_auto)
            && ($this->mode_rubrique ?? 'manuel') === 'automatique'
            && trim((string) $this->formule) !== '';
    }

    /** Charge employeur (CNSS, ANPE ou rubrique « Patronal ») : jamais dans le brut, les retenues ni le net. */
    public function estPatronale(): bool
    {
        return $this->sens_rubrique === 'Patronal';
    }

    /** Rubriques communes aux enseignants et au personnel, ou propres à l'enseignant. */
    public function scopePourEnseignant($query)
    {
        return $query->whereIn('categorie_rubrique', ['Commun', 'Enseignant']);
    }

    /** Rubriques communes aux enseignants et au personnel, ou propres au personnel. */
    public function scopePourPersonnel($query)
    {
        return $query->whereIn('categorie_rubrique', ['Commun', 'Personnel']);
    }
}
