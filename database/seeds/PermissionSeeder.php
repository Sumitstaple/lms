<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	$data = array(

    		array( 'code' => 'admin_permission',
            'name' => 'User can access dashboard',
            'description' => 'When users have this permission. They can access dashboard functionality.',
            'sort_order' => 1),

    		array( 'code' => 'user_permission',
            'name' => 'User can access front end',
            'description' => 'User can access front end ',
            'sort_order' => 1),
            
            array( 'code' => 'league_admin',
            'name' => 'League Admin',
            'description' => 'League Admin ',
            'sort_order' => 1),

            array( 'code' => 'super_admin',
            'name' => 'Super Admin',
            'description' => 'Super Admin ',
            'sort_order' => 1)
    	);

    	foreach ($data as $key => $value) {
    		# code...
    	

        $Permission = \App\Laravue\Models\Permission::create([
            'code' => $value['code'],
            'name' => $value['name'],
            'description' => $value['description'],
            'sort_order' => 1,
        ]);
    }
    }
}
