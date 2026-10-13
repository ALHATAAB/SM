<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionModel extends Model
{
    use HasFactory;
    protected $fillable = ["nom","slug","groupBy"] ;
    protected $table = 'permission_models';

    static public function getRecord()
    {
        $getPermission= PermissionModel::groupBy('groupBy')->get();
        $result=array();
        foreach ($getPermission as $value) {
            $getPermissionGroup= PermissionModel::getPermissionGroup($value->groupBy);
            $data=Array();
            $data['id']=$value->id;
            $data['nom']=$value->nom;
            $group=Array();
            foreach ($getPermissionGroup as $valueG) {
            $dataG=Array();
            $dataG['id']=$valueG->id;
            $dataG['nom']=$valueG->nom;
            $group[]=$dataG;
            }
            $data['group']=$group;
            $result[]=$data;
        }
        return $result;
    }
    static public function getPermissionGroup($groupBy){
        return PermissionModel::where('groupBy',$groupBy)->get();
    }
}
