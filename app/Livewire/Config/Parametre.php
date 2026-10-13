<?php

namespace App\Livewire\Config;

use App\Models\Config\ConfigMatriculeModel;
use App\Models\PaieRH\MontantTrancheFiscaleModel;
use App\Models\PaieRH\TauxAncienneteModel;
use App\Models\PaieRH\TauxChargeFamillialeModel;
use App\Models\PaieRH\TauxTrancheFiscaleModel;
use Livewire\Component;

class Parametre extends Component
{
    public $config_id,$initiale,$type,$nbr_caractere;
    public $CountConfigApprenant,$CountConfigEnseignant,$CountConfigEmployer;
    public $ConfigMatriculeApprenants=[] ;
    public $ConfigMatriculeEnseignants=[] ;
    public $ConfigMatriculeEmployers=[] ;

    
    
    public function mount()
    {
        $this->ConfigMatriculeApprenants=ConfigMatriculeModel::where('type','Apprenant')->get();
        $this->CountConfigApprenant=ConfigMatriculeModel::where('type','Apprenant')->count();
        
        $this->ConfigMatriculeEnseignants=ConfigMatriculeModel::where('type','Enseignant')->get();
        $this->CountConfigEnseignant=ConfigMatriculeModel::where('type','Enseignant')->count();
        
        $this->ConfigMatriculeEmployers=ConfigMatriculeModel::where('type','Employer')->get();
        $this->CountConfigEmployer=ConfigMatriculeModel::where('type','Employer')->count();


       
    }

    public function render()
    {
        return view('livewire.config.parametre');
    }

    public function addCongifMatriculeApprenant()
    {
        $validated = $this->validate(
            [
                'initiale'=>'required',
                'nbr_caractere'=>'required',
            ]
        );
        if(ConfigMatriculeModel::where('type','Apprenant')->count())
        {
            return redirect('/parametre')->with('danger','donnée existe déjà');
        }else{
            ConfigMatriculeModel::create([
                'initiale'=>$validated['initiale'],
                'type'=>'Apprenant',
                'nbr_caractere'=>$validated['nbr_caractere'],
            ]);

            return redirect('/parametre')->with('success','Opération effectuée avec succès');
        }
        
    }
    public function editerConfigMatriculeApprenant($id){

        $configInitialeApprenant = ConfigMatriculeModel::find($id);
        $this->config_id= $id;
        $this->initiale= $configInitialeApprenant->initiale;
        $this->nbr_caractere= $configInitialeApprenant->nbr_caractere;

    }

    public function updateCongifMatriculeApprenant()
    {
   
        $configInitialeApprenant = ConfigMatriculeModel::find($this->config_id);
        
        $validated = $this->validate(
            [
                'initiale'=>'required',
                'nbr_caractere'=>'required',
            ]
        );

        $configInitialeApprenant->update([
            'initiale'=>$validated['initiale'],
            'nbr_caractere'=>$validated['nbr_caractere'],
        ]);

        return redirect('/parametre')->with('info','Modification effectuée avec succès');
    }

    public function addCongifMatriculeEnseignant()
    {
        $validated = $this->validate(
            [
                'initiale'=>'required',
                'nbr_caractere'=>'required',
            ]
        );
        if(ConfigMatriculeModel::where('type','Enseignant')->count())
        {
            return redirect('/parametre')->with('danger','donnée existe déjà');
        }else{
            ConfigMatriculeModel::create([
                'initiale'=>$validated['initiale'],
                'type'=>'Enseignant',
                'nbr_caractere'=>$validated['nbr_caractere'],
            ]);

            return redirect('/parametre')->with('success','Opération effectuée avec succès');
        }
        
    }
    public function editerConfigMatriculeEnseignant($id){

        $configInitialeApprenant = ConfigMatriculeModel::find($id);
        $this->config_id= $id;
        $this->initiale= $configInitialeApprenant->initiale;
        $this->nbr_caractere= $configInitialeApprenant->nbr_caractere;

    }

    public function updateCongifMatriculeEnseignant()
    {
   
        $configInitialeApprenant = ConfigMatriculeModel::find($this->config_id);
        
        $validated = $this->validate(
            [
                'initiale'=>'required',
                'nbr_caractere'=>'required',
            ]
        );

        $configInitialeApprenant->update([
            'initiale'=>$validated['initiale'],
            'nbr_caractere'=>$validated['nbr_caractere'],
        ]);

        return redirect('/parametre')->with('info','Modification effectuée avec succès');
    }

    public function addCongifMatriculeEmployer()
    {
        $validated = $this->validate(
            [
                'initiale'=>'required',
                'nbr_caractere'=>'required',
            ]
        );
        if(ConfigMatriculeModel::where('type','Employer')->count())
        {
            return redirect('/parametre')->with('danger','donnée existe déjà');
        }else{
            ConfigMatriculeModel::create([
                'initiale'=>$validated['initiale'],
                'type'=>'Employer',
                'nbr_caractere'=>$validated['nbr_caractere'],
            ]);

            return redirect('/parametre')->with('success','Opération effectuée avec succès');
        }
        
    }
    public function editerConfigMatriculeEmployer($id){

        $configInitialeEmployer = ConfigMatriculeModel::find($id);
        $this->config_id= $id;
        $this->initiale= $configInitialeEmployer->initiale;
        $this->nbr_caractere= $configInitialeEmployer->nbr_caractere;

    }

    public function updateCongifMatriculeEmployer()
    {
   
        $configInitialeEmployer = ConfigMatriculeModel::find($this->config_id);
        
        $validated = $this->validate(
            [
                'initiale'=>'required',
                'nbr_caractere'=>'required',
            ]
        );

        $configInitialeEmployer->update([
            'initiale'=>$validated['initiale'],
            'nbr_caractere'=>$validated['nbr_caractere'],
        ]);

        return redirect('/parametre')->with('info','Modification effectuée avec succès');
    }
}
