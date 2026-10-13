<?php

namespace App\Models\PaieRH;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\PaieRH\RubriqueModel;
use App\Models\Scolarite\AnneeScolaireModel;
use Illuminate\Database\Eloquent\Model;

class BulletinCalculerModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'bulletin_calculer_models';

    public function employer()
    {
        return $this->belongsTo(EmployerModel::class);
    }

    public function mois()
    {
        return $this->belongsTo(MoisPaieModel::class);
    }

    public function annee()
    {
        return $this->belongsTo(AnneeScolaireModel::class);
    }

    public function rubrique()
    {
        return $this->belongsTo(RubriqueModel::class);
    }
}
