<?php

namespace App\Helper;
use Modules\Sport\Models\NotificationTemplate;
use Modules\Sport\Models\Notification;
use Auth;

class NotificationHandler 
{
    public static function save($contant, $value, $data ) {
       $desc = NotificationTemplate::where('slug', $data['slug'])->where('mode','system')->where('is_active','yes')->value('long_description');
       if(!empty($desc)) {
       	  $replacedStr = str_replace($contant, $value, $desc);
       	  $id = $data['userId'];
       	  
       	  $insert = array(
              'user_id' => $id,
              'message' => $replacedStr,
              'is_read' => 'no',
              'type' => $data['type'],
              'target_tbl' => $data['targetTable'],
              'target_column' => $data['targetCol'],
              'target_id' => $data['targetId'],
              'league_id' => isset($data['leagueId'])?$data['leagueId']:0
       	  );
       	  Notification::create($insert);
       }
    }
}
