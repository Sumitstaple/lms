<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helper\NotificationHandler;
use Mail;

class NotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    	$now = date('Y-m-d H:i:s');
        $rounds = DB::table('lms_rounds')->where('start_datetime','>=',$now)->get();
        $leagueId = array();
        if(!$rounds->isEmpty()) {
        	$sportsId = array();
        	$roundsId = array(); 
        	foreach ($rounds as $key => $value) {
        		$sportsId[] = $value->sport_id;
        		$roundsId[] = $value->id;
        	}

        	$League = DB::table('lms_leagues')->where('status','active')->whereIn('sport_id',$sportsId)->get();
        	if(!$League->isEmpty()) {
        		foreach ($League as $key => $value) {
        			if(in_array($value->round_to_start,$roundsId) || in_array($value->current_round_id,$roundsId)) {
        			if(!in_array($value->id, $leagueId)) {
        			  $leagueId[] = $value->id;	
        			 }	
        			}
        		}
        	}
        }
        $LeageuAndUser = DB::table('lms_league_users')->where('is_team_pickup','no')->where('is_knockedout','no')->whereIn('league_id',$leagueId)->get();
        if(!($LeageuAndUser)->isEmpty()) {
        	foreach ($LeageuAndUser as $key => $value) {
        		$slug = 'team_not_picked';
				$this->triggerNotification($slug, $value->user_id, $value->sport_id, $value->league_id);
				$implode = "User Id=> ".$value->user_id."Sport Id=> ".$value->sport_id."League ID".$value->league_id;
				\Log::info($implode);
        	}
        	\Log::info("Team Not Picked Cron is Working");
        }

       //$this->sendWinnerEmail(); 
    }
    public function triggerNotification($slug, $userid, $targetId, $l_id) {
    	     $name = DB::table('lms_leagues')->find($l_id)->league_name;
    	     $constantArray = array('{{leaugeName}}');
             $valueArray = array($name);
             $notify = array(
                'slug' => $slug,
                'userId' => $userid,
                'type' => 'teamNotPicked',
                'targetTable' => 'lms_sports',
                'targetCol' => 'sport_icon',
                'targetId' => $targetId,
                'leagueId' => $l_id
             );
             NotificationHandler::save($constantArray , $valueArray, $notify);
    }

    public function sendWinnerEmail() {
      $all = DB::table('lms_mail_notification')->where('is_sent','no')->get()->toArray();
      if(!empty($all)) {
      	foreach ($all as $key => $value) {
      		$name = DB::table('users')->find($value['user_id']);
      		$competition = DB::table('lms_leagues')->find($value['league_id']);
      		if($competition->type=='lms') {
				$data['competion'] = $competition->league_name;
				$data['user'] = $name->first_name;
				$data['to'] = $name->email;
				Mail::send('emails.winner', $data, function($message) use ($data) {
				 $message->to($data['to'], 'Congratulation!')->subject
				    ('Congratulation!');
				});
				DB::table('lms_mail_notification')->where('id',$value['id'])->update(['is_sent'=> "yes"]);	
      		}
      		
      	}
      }
    }
}
