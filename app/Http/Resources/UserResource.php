<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Laravue\Models\Permission;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // print_r(explode(",",$this->access_permissions)); die;
        $getAllPermissions = Permission::whereIn('id',explode(",",$this->access_permissions))->pluck('code');
        
        $role = 'admin';
        if ($this->role_id == 2) {
            $role = 'customer'; 
        }
        if($this->referral_league > 0){
            $league_id = $this->referral_league;
        } else {
            $league_id = 0;
        }
        return [
            'id' => $this->id,
            'name' => $this->first_name.' '.$this->last_name,
            'email' => $this->email,
            'is_marketing_option_active' => $this->is_marketing_option_active == 'yes'?true:false,
            'is_notifications_option_active' => $this->is_notifications_option_active == 'yes'?true:false,
            // 'roles' => array_map(
            //     function ($role) {
            //         return $role['name'];
            //     },
            //     $this->roles->toArray()
            // ),
            // 'permissions' => array_map(
            //     function ($permission) {
            //         return $permission['name'];
            //     },
            //     $this->getAllPermissions()->toArray()
            // ),
            'roles' => (array('0' => $role)),
            'role' => $role,
            'nickname' => $this->nickname,
            'avatar' => $this->avatar,
            'is_mark_admin' => $this->is_mark_admin,
            'permissions' => $getAllPermissions,
            'league_id' => $league_id,
        ];
    }
}
