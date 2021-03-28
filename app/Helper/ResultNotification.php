<?php

namespace App\Helper;
use Modules\Sport\Models\NotificationTemplate;
use Modules\Sport\Models\Notification;
use Modules\Sport\Models\League;
use Modules\Sport\Models\Round;
use Modules\Frontend\Models\UsersLeague;
use App\Helper\NotificationHandler;
use Modules\Frontend\Models\SavedTeam;
use Modules\Sport\Models\NotificationTemplate;
use Modules\Result\Models\mailNotification;
use Auth;

class ResultNotification
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

   public function getLeagueUser($id) {
   	$return = array();
   	$return = UsersLeague::where('league_id',$id)->where('status','active')->pluck('user_id')->toArray();
   	return $return;
   }
    public function resultNotification( $round ) {
    	$sportId = Round::find($round)->sport_id;
        $LeagueList = League::where('sport_id',$sportId)->where('current_round_id',$round)->where('status','active')->get()->toArray();

       foreach ($LeagueList as $key => $value) {
       	   //$leagueDetail = League::where('id',$value['league_id'])->where('status','active')->first();
       	   $users = $this->getLeagueUser($value['id']);
	       	foreach ($users as $ke => $val) {
	       	 $lTeam = SavedTeam::where('user_id',$val)->where('league_id',$value['id'])->where('round_id',$round)->first();
	       	   if(!is_null($lTeam->status)) {
                  $slug = $this->getSlug($lTeam->status).'_'.$value['type'];
                  if($slug=='win_lml') {
                  	$this->triggerNotification($slug, $val,$lTeam->fixture_id,$value['id'], $round);
                  	$this->mailNotificationHandler($slug, $val,$value['id'], $round);
                  } else {
                  	$this->triggerNotification($slug, $val,$lTeam->fixture_id,$value['id']);
                  }

	       	   } else {
                  if($leagueDetail->if_forfeit=='knocked_out') {
                   $twer = UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->first()->is_knockedout;
                   if($twer=='yes') {
                   	 $slug = 'loss_'.$value['type'];
                     $this->triggerNotification($slug, $val,1,$value['id']);
                   }
	       	     }
	       	   }
	       	}
       }

    }
    public function getSlug($str) {
	     if($str=='w') {
	     	return 'win';
	     } else {
	     	return 'loss';
	     }
    }
    public function triggerNotification($slug, $userid, $fixture_id, $l_id,$round=null) {
    	    $legueName = League::find($l_id)->league_name;
             if(is_null($round)) {
    	      $constantArray = array('{{leaugeName}}');
              $valueArray = array($legueName);
            } else {
                $roundNumber =  Round::find($round)->round_number;
            	$constantArray = array('{{roundNumber}}','{{leaugeName}}');
                $valueArray = array($roundNumber,$legueName);
            }
             $targetId = Fixture::where('id',$fixture_id)->value('sport_id');
             $notify = array(
                'slug' => $slug,
                'userId' => $userid,
                'type' => 'result',
                'targetTable' => 'lms_sports',
                'targetCol' => 'sport_icon',
                'targetId' => $targetId,
                'leagueId' => $l_id
             );
             NotificationHandler::save($constantArray , $valueArray, $notify);
      }
    }
    public function mailNotificationHandler($slug, $userid, $l_id, $r_id) {
     $userTeam = SavedTeam::where('user_id',$userid)->where('league_id',$l_id)->where('round_id',$r_id)->value('team_id');
     if(!empty($userTeam)) {
     	$desc = NotificationTemplate::where('slug', $slug)->where('mode','mail')->where('is_active','yes')->value('long_description');
         if(!empty($desc)) {
         	$leagueName = League::find($l_id)->league_name;
         	$teamName = Team::find($userTeam)->team_name;
         	$userName = User::find($userid)->first_name;
            $constantArray = array('{{firstName}}','{{leagueName}}','{{teamName}}');
            $valueArray = array($userName,$leagueName,$teamName);
            $replacedStr = str_replace($constantArray, $valueArray, $desc);
            $store = array(
               'user_id' => $userid,
               'league_id' => $l_id,
               'message' => $replacedStr
            );
            mailNotification::create($store);
         }
     }
    }
}
