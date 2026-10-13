<?php

namespace App\Models\PaieRH;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\RubriqueModel;
use Illuminate\Database\Eloquent\Model;

class ModelBulletinModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'model_bulletin_models';

    protected $casts = ['permanente' => 'boolean'];

    /** Rubrique « ce mois seulement » (non permanente). */
    public function estPonctuelle(): bool
    {
        return !$this->permanente;
    }

    public function rubrique()
    {
        return $this->belongsTo(RubriqueModel::class);
    }

    public function employer()
    {
        return $this->belongsTo(EmployerModel::class);
    }
}
