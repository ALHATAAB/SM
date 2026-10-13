<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePermissionModel extends Model
{
    use HasFactory;
    protected $fillable = ["permission_id","role_id"] ;
    protected $table = 'role_permission_models';

    static public function InsertUpdateRecord($permission_ids,$role_id){
        RolePermissionModel::where('role_id',$role_id)->delete();
        foreach ($permission_ids as $permission_id){
            $save= new RolePermissionModel();
            $save->permission_id=$permission_id;
            $save->role_id=$role_id;
            $save->save();
        }

    }

    static public function getRolePermission($role_id){

        return RolePermissionModel::where('role_id',$role_id)->get();
    }

    static public function getPermission($slug , $role_id){

        return RolePermissionModel::select('role_permission_models.id')
        ->join('permission_models', 'permission_models.id', '=', 'role_permission_models.permission_id')
        ->where('role_permission_models.role_id', $role_id)
        ->where('permission_models.slug', '=', $slug)
        ->count();

    }
}
