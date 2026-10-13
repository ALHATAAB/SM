<?php

namespace App\Livewire\Auth;

use App\Models\Auth\PermissionModel;
use App\Models\Auth\RolePermissionModel;
use App\Models\Auth\PermissionRole;
use App\Models\Auth\RoleModel;
use Livewire\Component;

class RolePermissionEdit extends Component
{
    public $roles=[];
    public $donnee = [];  // Pour conserver les valeurs des champs
    public $nom;
    public $getPermission = [];
    public $getRolePermission = [];
    public array $permission_id= [];
    public $role;

     public function mount(RoleModel $role, $id)
    {
        $id = decrypt($id);
        $roles = RoleModel::find($id);
        $this->getPermission = PermissionModel::getRecord();
        $data['getPermission']=$this->getPermission;
        $this->getRolePermission=RolePermissionModel::getRolePermission($id);
         $data['getRolePermission']= RolePermissionModel::getRolePermission($id);
        $this->role = $id;
        $this->nom = $roles->nom;

         // Récupérer les permissions existantes pour ce rôle
            $this->permission_id = RolePermissionModel::where('role_id', $id)
            ->pluck('permission_id')
            ->toArray();

    }

    public function render()
    {
        return view('livewire.auth.role-permission-edit');
    }

    public function update()
    {
        // $data['getPermission']=$this->getPermission;
        // $this->permission_id=$data['getPermission'] ;
        // dd($this->permission_id) ;
        $save = RoleModel::find($this->role);
        $validated = $this->validate(
            [
                'nom' => 'required',
            ]
        );

        $save->nom = $validated['nom'];
        $save->save();

        RolePermissionModel::InsertUpdateRecord($this->permission_id,$save->id);

        return redirect('/role/permission')->with('success', 'Opération modifié avec succès');
    }

}
