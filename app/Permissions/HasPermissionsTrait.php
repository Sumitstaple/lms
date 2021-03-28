<?php 

namespace App\Permissions;

use App\Laravue\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Laravue\JsonResponse;
use \App\Laravue\Model\User;
use Auth;
use App\Http\Resources\UserResource;


trait HasPermissionsTrait {

   public function haspermission($code=array()) {

   	$user = auth()->guard('api')->user();

    $accesspermissions = $user->access_permissions;

    $accesspermissions = explode(',',$accesspermissions);
    
    $permissions_code = $this->getAllPermissions($accesspermissions);
    if(array_intersect($code,$permissions_code)){
    	return true;
    }
    else{
    	return false;
    }
  }

  public function getAllPermissions($ids){
  	$getAllPermissions = Permission::whereIn('id',$ids)->pluck('code')->toArray();
  	return $getAllPermissions;
  }

}