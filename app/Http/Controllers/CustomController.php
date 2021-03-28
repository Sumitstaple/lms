<?php
/**
 * File UserController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */ 

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Permission;
use App\Laravue\Models\Role;
use App\Laravue\Models\User;
use App\Laravue\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Image;
use File;
use DB;
/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class CustomController extends Controller
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */

   

    public function createUser(Request $request)
    {
        //User::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $passwordStr = '123456789';
        $password = Hash::make($passwordStr);
        $Getpermission = PermissionGroup::where('id',2)->first();
        $permission_ids = $Getpermission['permission_ids'];
        $usercount = 5000;
        $total = 20000;
        $s = 1;
        $users = array();


        while($usercount <= $total) {
          $users = array();
          for ($u=$s; $u <= $usercount; $u++) {
              $data = array();
              $data['first_name'] = 'user '.$u;
              $data['last_name'] = 'jhon';
              $data['email'] = 'league_user'.$u.'@yopmail.com';
              $data['password'] = $password;
              $data['access_permissions'] = $permission_ids;
              $data['role_id'] = 2;
              $data['is_mark_admin'] = 'no';
              $users[] = $data;
          }
          $s = $u;
          $usercount = $usercount+5000;
          User::insert($users);
        }
        echo $total.' user has been created.';

    }


}
