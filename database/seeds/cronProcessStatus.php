<?php

use Illuminate\Database\Seeder;
use Modules\Result\Models\cronProcessLog;
class cronProcessStatus extends Seeder
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
    	       'process_name' => 'lms_forfeit'
             ),
    		array( 
    	       'process_name' => 'lml_forfeit'
             ),
    		array( 
    	       'process_name' => 'lms_result'
             ),
    		array( 
    	       'process_name' => 'lml_result',
             ),
    		array( 
    	       'process_name' => 'undu_process',
             ),
    	);
    	foreach ($data as $key => $value) {
	        cronProcessLog::create([
	            'process_name' => $value['process_name']
	        ]);
        }
    }
}
