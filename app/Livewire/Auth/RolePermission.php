<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Auth\RoleModel;

class RolePermission extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $nom;

    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $roles = RoleModel::where("nom",'like','%'.$this->recherche.'%')
            ->paginate(5);
            return view('livewire.auth.role-permission',['roles'=>$roles]);
        }else{
            $roles = RoleModel::paginate(5);
            return view('livewire.auth.role-permission',['roles'=>$roles]);
        }
    }

}
