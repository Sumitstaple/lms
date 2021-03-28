<?php

namespace App\Helper;
use App\SystemLog;

use Auth;

class SystemLogs 
{
   
	public static function GenerateLogs($log_action,$description,$log_type){

		$SystemLogs = new SystemLog();

		$SystemLogs->user_id = Auth::user()->id;
		$SystemLogs->log_type_id = $log_type;
		$SystemLogs->log_action = $log_action;
		$SystemLogs->log_text = $description;
		$SystemLogs->save();

	}

 }
 

