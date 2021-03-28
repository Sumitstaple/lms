<?php

use App\Laravue\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Laravue\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
         // $user = User::find(1);
 
         // $user->update(['access_permissions' => 2]);

           $userList = array(

            array('name'=>"Admin",
            "access_permissions"=>1,
            "role_id"=>1),

            // array('name'=>"User",
            // "access_permissions"=>2,
            // "role_id"=>2),
        );

        foreach ($userList as $key => $value) {
            $name = $value['name'];
            $user = \App\Laravue\Models\User::create([
                'first_name' => $name,
                'last_name' => $name,
                'role_id' => $value['role_id'],
                'email' => strtolower($name) . '@laravue.dev',
                'password' => \Illuminate\Support\Facades\Hash::make('laravue'),
                'access_permissions'=> $value['access_permissions'],
            ]);
        }

    }
}
