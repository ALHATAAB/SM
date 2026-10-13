<?php

namespace App\Livewire\Comptabilite;

use App\Models\Auth\User;
use App\Models\Comptabilite\CaisseModel;
use Livewire\Component;
use Livewire\WithPagination;

class Caisse extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    // public $users;
    public $users = [];
    public $user_id, $caisse_id,$caisse, $montant_min, $montant_max,$solde ;
    public $Permission;

    public function mount()
    {

        //liste des roles
        $this->users = User::all();

    }

    public function render()
    {
          //condition de recherche utilisateur
          if($this->recherche ){
            $caisses = CaisseModel::where("caisse",'like','%'.$this->recherche.'%')
            ->paginate(5);
            return view('livewire.comptabilite.caisse',['caisses'=>$caisses]);
        }else{
            $caisses = CaisseModel::paginate(5);
            return view('livewire.comptabilite.caisse',['caisses'=>$caisses]);
        }
    }

    public function addCaisse()
    {
        $validated = $this->validate(
            [
                'caisse'=>'required',
                // 'montant_min'=>'required',
                // 'montant_max'=>'required',
                'solde'=>'required',
                'user_id'=>'required|unique:caisse_models',
            ]
        );

        CaisseModel::create([
            'caisse'=>$validated['caisse'],
            // 'montant_min'=>$validated['montant_min'],
            // 'montant_max'=>$validated['montant_min'],
            'solde'=>$validated['solde'],
            'user_id'=>$validated['user_id'],
        ]);

        return redirect('/caisse')->with('success','Opération effectuée avec succès');
    }

    //editer un utilisateur
    public function editCaisse($id){

        $caisse = CaisseModel::find($id);
        $this->caisse_id= $id;
        $this->caisse= $caisse->caisse;
        $this->montant_min= $caisse->montant_min;
        $this->montant_max= $caisse->montant_max;
        $this->user_id= $caisse->user_id;
    }
    public function updateCaisse()
    {
        $caisse = CaisseModel::find( $this->caisse_id);
        $validated = $this->validate(
            [
                'caisse'=>'required',
                'montant_min'=>'required',
                'montant_max'=>'required',
                'user_id'=>'required|unique:caisse_models,user_id,'.$caisse->id,
            ]);

        $caisse->update([
            'caisse'=>$validated['caisse'],
            'montant_min'=>$validated['montant_min'],
            'montant_max'=>$validated['montant_min'],
            'user_id'=>$validated['user_id'],
        ]);
        return redirect('/caisse')->with('info','Modification effectuée avec succès');
    }
}
