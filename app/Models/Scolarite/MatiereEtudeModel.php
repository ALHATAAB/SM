<?php

namespace App\Models\Scolarite;

use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\NoteApprenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatiereEtudeModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'matiere_etude_models';
    
    public function notes()
    {
        return $this->hasMany(NoteApprenantModel::class);
    }

    public function seances() {
        return $this->hasMany(SeanceModel::class);
    }
}
