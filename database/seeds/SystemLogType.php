<?php

use Illuminate\Database\Seeder;
use App\LogType;

class SystemLogType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $data = array(
    		array( 
    	    'type' => 'sports',
            'description' => 'This log related to sports',
            'sort_order' => 1,
          ),
          array( 
            'type' => 'teams',
            'description' => 'This log related to teams',
            'sort_order' => 2,
          ), 
         array( 
            'type' => 'rounds',
            'description' => 'This log related to rounds',
            'sort_order' => 3,
          ), 
         array( 
            'type' => 'fixtures',
            'description' => 'This log related to fixtures',
            'sort_order' => 4,
          ),
         array( 
            'type' => 'make_user_admin',
            'description' => 'This log related to make user admin',
            'sort_order' => 7,
          ),
          array( 
            'type' => 'result',
            'description' => 'This log related to Result Module',
            'sort_order' => 5,
          ),
          array( 
            'type' => 'team_selection',
            'description' => 'This log related to Team Selection',
            'sort_order' => 6
          )
    		
    	);

    	foreach ($data as $key => $value) {
	        $Permission = LogType::create([
	            'type' => $value['type'],
	            'description' => $value['description'], 
                'sort_order' => $value['sort_order'], 
	        ]);
        }
    }
}
