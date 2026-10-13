<?php

namespace App\Models\Scolarite;

use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\MatiereEtudeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteApprenantModel extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    protected $table = 'note_apprenant_models';

    public function apprenant()
    {
        return $this->belongsTo(InscriptionModel::class,'inscription_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(EnseignantModel::class,);
    }
    
    public function matiere() {
        return $this->belongsTo(MatiereEtudeModel::class,'matiere_etude_id');
    }

    public function classe() {
        return $this->belongsTo(ClasseEtudeModel::class,'classe_etude_id');
    }
    

}
