<?php

namespace App\Models\Scolarite;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Programme\SeanceModel;
use Illuminate\Database\Eloquent\Model;

class EnseignantModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'enseignant_models';

    public function seances() {
        return $this->hasMany(SeanceModel::class);
    }
}
