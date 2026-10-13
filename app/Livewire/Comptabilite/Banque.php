<?php

namespace App\Livewire\Comptabilite;
use App\Models\Comptabilite\BanqueModel;
use Livewire\Component;
use Livewire\WithPagination;

class Banque extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    // public $users;
    public $users = [];
    public $user_id, $banque_id,$banque, $numero_compte,$solde ;
    public $Permission;

    public function render()
    {
        //condition de recherche utilisateur
        if($this->recherche ){
            $banques = BanqueModel::where("banque",'like','%'.$this->recherche.'%')
            ->paginate(5);
            return view('livewire.comptabilite.banque',['banques'=>$banques]);
        }else{
            $banques = BanqueModel::paginate(5);
            return view('livewire.comptabilite.banque',['banques'=>$banques]);
        }
    }

    public function addbanque()
    {
        $validated = $this->validate(
            [
                'banque'=>'required',
                'numero_compte'=>'required|unique:banque_models',
                'solde'=>'required',
            ]
        );

        BanqueModel::create([
            'banque'=>$validated['banque'],
            'numero_compte'=>$validated['numero_compte'],
            'solde'=>$validated['solde'],
        ]);

        return redirect('/banque')->with('success','Opération effectuée avec succès');
    }

    //editer un utilisateur
    public function editbanque($id){

        $banque = BanqueModel::find($id);
        $this->banque_id= $id;
        $this->banque= $banque->banque;
        $this->numero_compte= $banque->numero_compte;

    }
    public function updatebanque()
    {
        $banque = BanqueModel::find( $this->banque_id);
        $validated = $this->validate(
            [
                'banque'=>'required',
                'numero_compte'=>'required',
            ]);

        $banque->update([
            'banque'=>$validated['banque'],
            'numero_compte'=>$validated['numero_compte'],
        ]);
        return redirect('/banque')->with('info','Modification effectuée avec succès');
    }
}
