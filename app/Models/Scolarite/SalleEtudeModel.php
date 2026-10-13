<?php

namespace App\Models\Scolarite;

use App\Models\Programme\SeanceModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalleEtudeModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'salle_etude_models';
    

    public function sceances() {
        return $this->hasMany(SeanceModel::class);
    }
}
