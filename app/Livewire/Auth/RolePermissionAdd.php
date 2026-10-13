<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Auth\RoleModel;
use App\Models\Auth\PermissionModel;
use Illuminate\Support\Facades\Request;
use App\Models\Auth\RolePermissionModel;

class RolePermissionAdd extends Component
{
    public $nom;
    public $getPermission = [];
    public array $permission_id = [];

    public function mount()
    {
        $this->getPermission = PermissionModel::getRecord();
        // $data['getPermission']=$this->getPermission;

    }
    public function render()
    {
        return view('livewire.auth.role-permission-add');
    }

    public function addRole(Request $request)
    {


        $validated = $this->validate(
            [
                'nom'=>'required',
            ]
        );
        $save=new RoleModel;
        $save->nom = $validated['nom'];
        $save->save();

        RolePermissionModel::InsertUpdateRecord($this->permission_id,$save->id);


        return redirect('/role/permission/add')->with('success','Opération effectuée avec succès');
    }

}
