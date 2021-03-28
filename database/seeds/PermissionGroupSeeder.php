<?php

use Illuminate\Database\Seeder;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(

    		array( 'role_id' => 3,
            'permission_ids' => 1,
            'name'=>'admin_permissions',
            'description'=>'This permission Group Used for admin'
            ),
    		array( 'role_id' => 2,
            'permission_ids' => 2,
            'name'=>'user_permissions',
            'description'=>'This permission Group Used for user'
            ),
            array( 'role_id' => 2,
            'permission_ids' => 2,
            'name'=>'league_admin',
            'description'=>'This permission Group Used for league admin.'
            )
    	);

    	foreach ($data as $key => $value) {
    		# code...
    	

        $Permission = \App\Laravue\Models\PermissionGroup::create([
            'role_id' => $value['role_id'],
            'permission_ids' => $value['permission_ids'],
            'name' => $value['name'],
            'description' => $value['description'],
            
        ]);
    }
    }
}
