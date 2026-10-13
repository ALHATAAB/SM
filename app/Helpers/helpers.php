<?php

use App\Livewire\Scolarite\ClasseEtude;
use App\Models\Auth\RolePermissionModel;
use App\Models\Comptabilite\CaisseModel;
use App\Models\Comptabilite\DateComptableModel;
use App\Models\Config\ConfigDateComptableModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use App\Models\Scolarite\SalleEtudeModel;
use Illuminate\Support\Facades\Auth;




function AnneeScolaire(){
return AnneeScolaireModel::where('etat',"actif")->first();
}

// Mode de gestion de la date comptable : 'MANUELLE' (défaut) ou 'AUTOMATIQUE'
function ModeDateComptable(){
    try {
        $config = ConfigDateComptableModel::first();
        return $config && $config->mode === 'AUTOMATIQUE' ? 'AUTOMATIQUE' : 'MANUELLE';
    } catch (\Throwable $e) {
        // table pas encore migrée : on reste en manuel
        return 'MANUELLE';
    }
}

// Mode automatique : la journée comptable = date du jour (créée/ouverte au besoin, les autres sont fermées)
function JourneeDuJour(){
    $annee = AnneeScolaire();
    if (!$annee) {
        return null;
    }

    $aujourdhui = now()->toDateString();

    $journee = DateComptableModel::firstOrCreate(
        ['date_comptable' => $aujourdhui],
        ['annee_scolaire_id' => $annee->id, 'statuts' => 'OUVERTE']
    );

    if ($journee->statuts !== 'OUVERTE') {
        $journee->update(['statuts' => 'OUVERTE']);
    }

    DateComptableModel::where('id', '!=', $journee->id)
        ->where('statuts', 'OUVERTE')
        ->update(['statuts' => 'FERMEE']);

    return $journee;
}

function JourneeOuverte($rafraichir = false){
    static $journee = null;
    static $chargee = false;

    if ($rafraichir) {
        $chargee = false;
    }
    if ($chargee) {
        return $journee;
    }
    $chargee = true;

    if (ModeDateComptable() === 'AUTOMATIQUE') {
        $journee = JourneeDuJour();
        if ($journee) {
            return $journee;
        }
    }

    return $journee = DateComptableModel::where('statuts',"OUVERTE")->first();
}

function soldeCaisse(){
    $countCaisse = CaisseModel::where('user_id',Auth::user()->id)->count();
    if($countCaisse > 0){
        $caisse = CaisseModel::where('user_id',Auth::user()->id)->first();
        if($caisse->etat == "ouverte"){
            return  CaisseModel::where('user_id',Auth::user()->id)->first();
        }
    }
}

function CountNiveau(){
    return NiveauEtudeModel::count();
}
function CountClasse(){
    return ClasseEtudeModel::count();
}
function CountMatiere(){
    return MatiereEtudeModel::count();
}
function CountFiliere(){
    return FiliereEtudeModel::count();
}
function CountSalle(){
    return SalleEtudeModel::count();
}
function Permission($param){
    if (!empty(Auth::check())) {
        return RolePermissionModel::getPermission($param,Auth::user()->role_id);
    }
    return redirect('/');
}



?>
