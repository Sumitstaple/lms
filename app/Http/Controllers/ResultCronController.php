<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Modules\Sport\Models\Round;
use Modules\Sport\Models\Fixture;
use Modules\Sport\Models\League;
use Modules\Sport\Models\Sport;
use Modules\Frontend\Models\UsersLeague;
use Modules\Frontend\Models\SavedTeam;
use App\Repositories\ResultRepository;
use Modules\Result\Models\userStatsUndu;
use App\Helper\ResultNotification;
use App\Helper\SystemLogs;
use App\Helper\NotificationHandler; 
use Modules\Result\Models\cronProcessLog;
use Modules\Sport\Models\Team;
use Modules\Sport\Models\ManageTeams;
use Modules\Result\Models\userStats;
use Modules\Sport\Models\NotificationTemplate;
use Modules\Result\Models\mailNotification;
use App\User;
use Modules\Result\Models\leagueWinner;


class ResultCronController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */


    private $LeagueChunkCounter = 50;
    private $waitforCronTimer = 5; //in Minute


    public function __construct(Request $request){

      if(isset($request->limit)){

        $this->LeagueChunkCounter = $request->limit;
      }


   }
   public function forFeitHandler() {
    //$this->ResultLMS();
   }
   public function Refresh() {
    //die('sdbv');
    $count = League::where('type','lms')->pluck('id')->toArray();

    foreach ($count as $key => $value) {
       if($key < 25) {
        League::where('id',$value)->update(['if_forfeit'=> "knocked_out",'crn_forfeit_checked' => "no"]);
       } else {
        League::where('id',$value)->update(['if_forfeit' => "assign_lowest_team",'crn_forfeit_checked' => "no"]);
       }
    }
    $count = League::where('type','lml')->pluck('id')->toArray();
    foreach ($count as $key => $value) {
       if($key < 25) {
        League::where('id',$value)->update(['if_forfeit'=> "knocked_out",'crn_forfeit_checked' => "no"]);
       } else {
        League::where('id',$value)->update(['if_forfeit' => "assign_lowest_team",'crn_forfeit_checked' => "no"]);
       }
    }
    echo "Done";

   }
   public function getLeagueUser($id) {
     $return = array();
    $return = UsersLeague::where('league_id',$id)->where('is_knockedout','no')->where('crn_forfeit_checked','no')->where('crn_result_process','no')->where('status','active')->pluck('user_id')->toArray();
    return $return;
   }
   public function updateRoundStats ($l, $u, $status, $r) {
       $lastStatus = UsersLeague::where('league_id',$l)->where('user_id', $u)->where('status','active')->value('result_stats');
       $update= '';
       if(is_null($lastStatus)) {
        $update = $status;
       } else {
         $update = $lastStatus.','.$status;
       }
       $return = array(
           'old' => $lastStatus,
           'new' => $update
       );
       return $return;
       //SavedTeam::where('user_id',$u)->where('league_id',$l)->where('round_id',$r)->update(['status' => $status]);
       //UsersLeague::where('league_id',$l)->where('user_id', $u)->where('status','active')->update(['result_stats' => $update]);
    }
    public function getTeamforAutoSelection($s_id, $l_id, $r_id, $u_id) {
     $allTeam = Team::where('sport_id',$s_id)->where('is_active','yes')->pluck('id')->toArray();
     $allRound = Round::where('sport_id',$s_id)->orderBy('id')->pluck('id')->toArray();
     $rountCounter = count( $allRound );
     $allTeamCounter = count($allTeam);
     $roundNumber = array_search($r_id, $allRound) + 1;
     $myTeam = array();
     if($roundNumber < $allTeamCounter || $roundNumber % $allTeamCounter == 0) {
        $myTeam = SavedTeam::where('user_id', $u_id)->where('league_id',$l_id)->where('round_id','!=',$r_id)->pluck('team_id')->toArray();
     } else {
      if( $roundNumber % $allTeamCounter == 1 ){
           $myTeam = array();
      }
      if( $roundNumber % $allTeamCounter > 1 ){
           $temp = abs($roundNumber - $roundNumber % $allTeamCounter);
           $num = range($temp, $roundNumber-2);
           $selectedRound = array();
           foreach ($num as $key => $val) {
             $selectedRound[] = $allRound[$val];
           }
          $myTeam = SavedTeam::where('user_id',$u_id)->where('league_id',$l_id)->whereIn('round_id',$selectedRound)->pluck('team_id')->toArray();
      }
     }
     $return = ManageTeams::where('sport_id',$s_id)->whereNotIn('team_id',$myTeam)->orderBy('rank_order', 'desc')->first()->team_id;
     return $return;
   }
   public function findFixtureByTeamId($l_id, $t_id) {
     $currentRound = League::where('id',$l_id)->value('current_round_id');
     $return = 1;
     if(!empty( $currentRound )) {
      $fixture = Fixture::where('round_id', $currentRound)->get()->toArray();
      if($fixture) {
        foreach ($fixture as $key => $value) {
          if($value['home_team_id']==$t_id || $value['away_team_id']==$t_id) {
            $return = $value['id'];
            break;
          }
        }
      }
     }
     return $return;
   }
   public function finalGD($gd, $l, $u) {
      $latest = userStats::where('user_id',$u)->where('league_id',$l)->orderBy('id', 'DESC')->first();
      $return= '';
      if(!empty($latest)) {
          $str = trim($latest->final_goal_difference) + $gd;
          $return = (string) $str;
      } else {
          $return = (string) $gd;
      }
      return $return;
    }
    public function cronDelayHandler($type) {
       $processStatus = cronProcessLog::where('process_name',$type)->first();
       $now = date('Y-m-d H:i:s');
       $nInterval = strtotime($now) - strtotime($processStatus->updated_at);
       $nInterval = floor($nInterval/60);
       if($nInterval >= $this->waitforCronTimer) {
        cronProcessLog::where('id',$processStatus->id)->where('last_batch_status','running')->update(['last_batch_status' => 'complete']);
        return 'true';
       } else {
        return 'false';
       }
    }
  public function forFeitHandlerLMS() {

    $this->LeagueChunkCounter = 100;

    $isReadyToRun = 'no';
    $strRunningProcess = cronProcessLog::where('process_status','running');
    $strRunningProcessCount  = $strRunningProcess->count();

    if($strRunningProcessCount > 1) {
      $isReadyToRun = 'no'; // not be allowed,  need to send developer alerts
      echo '<br/><h1 style="color:red">Not be allowed,  need to send developer alerts </h1>';
      return false;
    }else if($strRunningProcessCount == 0) {
      $isReadyToRun = 'yes'; // case when no process is running
    }


    // case when some other process is running
    if($strRunningProcessCount > 0 && $strRunningProcess->first()->process_name != 'lms_forfeit'){
    $isReadyToRun = 'no';
      return false;
    }

    $status = cronProcessLog::where('process_name','lms_forfeit')->first();

    if($status->process_status=='pending') {
      $isReadyToRun = 'yes';

    }else if($status->process_status=='running') {
      // check last batch if process already running
      if($status->last_batch_status=='complete'){
        $isReadyToRun = 'yes';
      }else if($status->last_batch_status=='running'){
        $isReadyToRun = 'no'; // shouldn't be allow to run
        $checkStatus = $this->cronDelayHandler('lms_forfeit');
        if($checkStatus!='true') {
          echo '<br/>======<h2 style="color:red">Just Wait, we are refreshing the system...</h2>';
        }
        return false;
      }
    }

  if($isReadyToRun =='yes'){

     $round = Round::where('result_process_status','wait_for_cron_process')->first();
     if($round) {

    // mark process running and batch status as running
    cronProcessLog::where('process_name','lms_forfeit')->update(['process_status' => "running", 'last_batch_status' => "running"]);

      $roundId = $round->id;
      $sportId = $round->sport_id;

      $leagues = League::where('current_round_id', $roundId)->where('type','lms')->where('crn_forfeit_checked','no')->where('crn_result_done','no')->limit($this->LeagueChunkCounter)->get()->toArray();
       if(!empty($leagues)) {
           foreach ($leagues as $key => $value) {
             $leaguesUser = $this->getLeagueUser($value['id']);

             if(!empty($leaguesUser)) {
              foreach ($leaguesUser as $ke => $val) {
                if (SavedTeam::where('league_id',$value['id'])->where('round_id',$roundId)->where('user_id',$val)->count() < 1){

                      if($value['if_forfeit']=='knocked_out') {
                         $stats = $this->updateRoundStats($value['id'], $val, 'l', $roundId);
                         UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->update(['is_knockedout'=> "yes",'result_stats' => $stats['new'],'crn_forfeit_checked' => "yes",'crn_result_process' => "yes",'last_round_stats' => $stats['old']]);
                         $slug = 'loss_lms';
                 $this->triggerNotification($slug, $val, 1,$value['id']);
                 // $this->mailNotification($slug, $val, $value['id'], $roundId);
                         echo "user=> ".$val." out from League".$value['id']."<br>";
                      } else {
                        $lowestTeam = $this->getTeamforAutoSelection($sportId, $value['id'], $roundId, $val);
                        $fixt = $this->findFixtureByTeamId($value['id'], $lowestTeam);
                        $fxtDetail = Fixture::find($fixt)->toArray();
              $insert = array(
                               'user_id' => $val,
                               'league_id' => $value['id'],
                               'round_id' => $roundId,
                               'team_id' => $lowestTeam,
                               'fixture_id' => $fixt
              );
              SavedTeam::create($insert);
              UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->update(['crn_forfeit_checked' => "yes",'is_team_pickup' => "yes"]);
              echo "user=> ".$val." assign Lowest Team from League".$value['id']."<br>";
                      }
          } else {
            UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->update(['crn_forfeit_checked' => "yes"]);
            echo "no User Found for League id => ".$value['id']." and user id => ".$val."<br>";
          }
         }
         League::where('id', $value['id'])->where('crn_forfeit_checked','no')->update(['crn_forfeit_checked'=> "yes"]);
              } else {
                League::where('id', $value['id'])->where('crn_forfeit_checked','no')->update(['crn_forfeit_checked'=> "yes"]);

                //echo "no User Found";
              }
             }
             cronProcessLog::where('process_name','lms_forfeit')->update(['last_batch_status' => "complete"]);
           } else {
            cronProcessLog::where('process_name','lms_forfeit')->update(['process_status' => "complete", 'last_batch_status' => "complete",'round_id' => $roundId]);
            Round::where('id',$roundId)->update(['result_process_status' => "running"]);
       echo '<br/>======<h2 style="color:red">No More LMS League Found!</h2>';
           }
       } else {
       echo '<br/>======<h2 style="color:red">No Round Available for Result!</h2>';
       }
  }else{
    echo '<br/>======<h2 style="color:red">Not Allow to run LMS ForFeit!</h2>';
    return false;
  }


     }

     public function forFeitHandlerLML() {
      $this->LeagueChunkCounter = 100;
          $isReadyToRun = 'no';
       $strRunningProcess = cronProcessLog::where('process_status','running');
       $strRunningProcessCount  = $strRunningProcess->count();
         if($strRunningProcessCount > 1) {
      $isReadyToRun = 'no'; // not be allowed,  need to send developer alerts
      echo '<br/><h1 style="color:red">Not be allowed,  need to send developer alerts </h1>';
      return false;
         }else if($strRunningProcessCount == 0) {
        $isReadyToRun = 'yes'; // case when no process is running
       }

      // case when some other process is running
      if($strRunningProcessCount > 0 && $strRunningProcess->first()->process_name != 'lml_forfeit'){
        $isReadyToRun = 'no';
          return false;
      }

        $status = cronProcessLog::where('process_name','lml_forfeit')->first();

      if($status->process_status=='pending') {
        $isReadyToRun = 'yes';

      }else if($status->process_status=='running') {

        // check last batch if process already running
        if($status->last_batch_status=='complete'){
          $isReadyToRun = 'yes';
        }else if($status->last_batch_status=='running'){
          $isReadyToRun = 'no'; // shouldn't be allow to run

            $checkStatus = $this->cronDelayHandler('lml_forfeit');

        }
      }

      $lastOneStatus = cronProcessLog::where('process_name','lms_forfeit')->first();
      if($isReadyToRun =='yes' && $lastOneStatus->process_status=='complete'){
        // mark process running and batch status as running
        cronProcessLog::where('process_name','lml_forfeit')->update(['process_status' => "running", 'last_batch_status' => "running"]);
      }else{
        return false;  // previous step not completed
      }

     $rid = $lastOneStatus->round_id;
     if($rid) {
      $roundId = $rid;
      $sportId = Round::where('id',$roundId)->value('sport_id');
      $leagues = League::where('current_round_id', $roundId)->where('type','lml')->where('crn_forfeit_checked','no')->where('crn_result_done','no')->limit($this->LeagueChunkCounter)->get()->toArray();
       if(!empty($leagues)) {
           foreach ($leagues as $key => $value) {
             $leaguesUser = $this->getLeagueUser($value['id']);
             if(!empty($leaguesUser)) {
              foreach ($leaguesUser as $ke => $val) {
                if (SavedTeam::where('league_id',$value['id'])->where('round_id',$roundId)->where('user_id',$val)->count() < 1){
                      if($value['if_forfeit']=='knocked_out') {
              $latest = userStats::where('user_id',$val)->orderBy('id', 'DESC')->first();
            $total = 0;
            if(!empty($latest)) {
            $total = $latest->total_points;
            }
            $new = new userStats;
            $new->user_id = $val;
            $new->debit_point = 0;
            $new->credit_point = 0;
            $new->total_points = $total;
            $new->league_id = $value['id'];
            $new->round_id = $roundId;
            $new->fixture_id = 1;
            $new->final_goal_difference = $this->finalGD(0, $value['id'], $val);
            $new->save();

                         $stats = $this->updateRoundStats($value['id'], $val, 'l', $roundId);
                         UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->update(['result_stats' => $stats['new'],'crn_forfeit_checked' => "yes",'crn_result_process'=> "yes",'last_round_stats' => $stats['old']]);
                          $slug = 'loss_lml';
            $this->triggerNotification($slug, $val, 1,$value['id'],$roundId);
            // $this->mailNotification($slug, $val, $value['id'], $roundId);
                      } else {
                        $lowestTeam = $this->getTeamforAutoSelection($sportId, $value['id'], $roundId, $val);
                        $fixt = $this->findFixtureByTeamId($value['id'], $lowestTeam);
                        $fxtDetail = Fixture::find($fixt)->toArray();
              $insert = array(
                               'user_id' => $val,
                               'league_id' => $value['id'],
                               'round_id' => $roundId,
                               'team_id' => $lowestTeam,
                               'fixture_id' => $fixt
              );
              SavedTeam::create($insert);
              UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->update(['crn_forfeit_checked' => "yes",'is_team_pickup' => "yes"]);
                      }
          } else {
            UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('status','active')->update(['crn_forfeit_checked' => "yes"]);
          }
         }
          League::where('id', $value['id'])->where('crn_forfeit_checked','no')->update(['crn_forfeit_checked'=> "yes"]);
              } else {

                League::where('id', $value['id'])->where('crn_forfeit_checked','no')->update(['crn_forfeit_checked'=> "yes"]);
                echo "no User Found";
              }
             }
             cronProcessLog::where('process_name','lml_forfeit')->update(['last_batch_status' => "complete"]);
       echo '<br/>======<h3 style="color:green">Batch Completed!</h2>';
           } else {
            cronProcessLog::where('process_name','lml_forfeit')->update(['process_status' => "complete", 'last_batch_status' => "complete",'round_id' => $roundId]);
       echo '<br/>======<h2 style="color:red">No More LML League Found!</h2>';
           }
       } else {
       echo '<br/>======<h2 style="color:red">No Round Available for Result!</h2>';
       }
     }

     public function updatePointsLMS( $data ) {
       if($data['status']=='complete') {
           if($data['winnerId']==$data['userTeam']) {

             $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
             SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'w']);
             UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'is_team_pickup'=>"no",'last_round_stats'=> $stats['old']]);

            //You win in this round notification
                $slug = 'win_lms';
              $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
             // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
             return true;
           } else {
            UsersLeague::where('league_id',$data['league_id'])->where('user_id',$data['user_id'])->where('status','active')->update(['is_knockedout'=> "yes"]);

              $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'l', $data['round_id']);
             SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'l']);
             UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'last_round_stats'=> $stats['old']]);
             //You lose in this round notification
             $slug = 'loss_lms';
             $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
             // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
             return true;
           }
       } else {
            if($data['status']=='draw'){
               UsersLeague::where('league_id',$data['league_id'])->where('user_id',$data['user_id'])->where('status','active')->update(['is_knockedout'=> "yes"]);
               $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'd', $data['round_id']);

               SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'd']);
               UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'last_round_stats' => $stats['old']]);
                $slug = 'loss_lms';
        $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
        // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
               return true;
            }

            if($data['status']=='abandon') {
              $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
                SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'w']);

               UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'is_team_pickup'=>"no",'last_round_stats'=> $stats['old']]);
               $slug = 'win_lms';
         $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
         // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
               return true;
            }

       }
    }
    public function getLeagueUserLMSLML($id) {
      $return = array();
      $return = UsersLeague::where('league_id',$id)->where('is_knockedout','no')->where('crn_forfeit_checked','yes')->where('crn_result_process','no')->where('status','active')->pluck('user_id')->toArray();
        return $return;
    }
    public function triggerNotification($slug, $userid, $fixture_id, $l_id,$round=null) {
          // echo '<br/>$slug='.$slug;
          // echo '<br/>$userid='.$userid;
          // echo '<br/>$fixture_id='.$fixture_id;
          // echo '<br/>$l_id='.$l_id;
          // echo '<br/>$round='.$round;
          // die("here");
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
     public function ResultLMS() {
      $this->LeagueChunkCounter = 70;
             $isReadyToRun = 'no';
       $strRunningProcess = cronProcessLog::where('process_status','running');
       $strRunningProcessCount  = $strRunningProcess->count();
    if($strRunningProcessCount > 1) {
      $isReadyToRun = 'no'; // not be allowed,  need to send developer alerts
      return false;
    }else if($strRunningProcessCount == 0) {
      $isReadyToRun = 'yes'; // case when no process is running
    }

      // case when some other process is running
      if($strRunningProcessCount > 0 && $strRunningProcess->first()->process_name != 'lms_result'){
        $isReadyToRun = 'no';
          return false;
      }

        $status = cronProcessLog::where('process_name','lms_result')->first();

      if($status->process_status=='pending') {
        $isReadyToRun = 'yes';

      }else if($status->process_status=='running') {

        // check last batch if process already running
        if($status->last_batch_status=='complete'){
          $isReadyToRun = 'yes';
        }else if($status->last_batch_status=='running'){
          $isReadyToRun = 'no'; // shouldn't be allow to run
          $checkStatus = $this->cronDelayHandler('lms_result');

	        if($checkStatus!='true') {
	        	echo '<br/>======<h2 style="color:red">Just Wait, we are refreshing the system...</h2>';
	        }
	        return false;
        }
      }

      $lastOneStatus = cronProcessLog::where('process_name','lms_forfeit')->first();
      $lastTwoStatus = cronProcessLog::where('process_name','lml_forfeit')->first();
      if($isReadyToRun =='yes' && $lastOneStatus->process_status=='complete' && $lastTwoStatus->process_status=='complete'){
        // mark process running and batch status as running
        cronProcessLog::where('process_name','lms_result')->update(['process_status' => "running", 'last_batch_status' => "running"]);
      }else{
        return false;
      }

        $rid = $lastOneStatus->round_id;
    if($rid) {
           $roundId = $rid;
           $sportId = Round::where('id',$roundId)->value('sport_id');
        $leagues = League::where('current_round_id', $roundId)->where('type','lms')->where('crn_forfeit_checked','yes')->where('crn_result_done','no')->limit($this->LeagueChunkCounter)->get()->toArray();

         if(!empty($leagues)) {

             foreach ($leagues as $key => $value) {
                  $users = $this->getLeagueUserLMSLML($value['id']);

                  if(!empty($users)) {
                      foreach ($users as $key => $val) {
                        $usrTeam = SavedTeam::where('league_id',$value['id'])->where('round_id',$roundId)->where('user_id',$val)->first();

             if($usrTeam) {
              $fixture = Fixture::find($usrTeam->fixture_id)->toArray();
              $asset = array(
                  'userTeam' => $usrTeam->team_id ,
                  'userFixture' => $usrTeam->fixture_id,
                  'league_id' => $value['id'],
                  'round_id' => $roundId,
                  'user_id' => $val,
                  'status' => $fixture['fixure_result_status'],
                  'winnerId' => $fixture['winner_team_id'],
                  'fixture_id' => $fixture['id']
              );
              if($this->updatePointsLMS($asset)) {
                UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('crn_forfeit_checked','yes')->where('crn_result_process','no')->update(['crn_result_process' => "yes"]);
                //echo "Result Proccessed for League=> ".$value['id']."User Id=>".$val."<br>";
              }
            }
                      }

                      League::where('id', $value['id'])->where('crn_forfeit_checked','yes')->where('crn_result_done','no')->update(['crn_result_done' => "yes"]);
                      $this->updateLeagueRound($value['id'], $roundId);
                  } else {
                    League::where('id', $value['id'])->where('crn_forfeit_checked','yes')->where('crn_result_done','no')->update(['crn_result_done' => "yes"]);
                    $this->updateLeagueRound($value['id'], $roundId);
                  }
             }
              cronProcessLog::where('process_name','lms_result')->update(['last_batch_status' => "complete"]);
        echo '<br/>======<h3 style="color:green">Batch Completed!</h2>';
         } else {
          cronProcessLog::where('process_name','lms_result')->update(['process_status' => "complete", 'last_batch_status' => "complete",'round_id' => $roundId]);
           echo '<br/>======<h2 style="color:red">No More LMS League Found!</h2>';
         }
     }
   }

  public function calculateGoalDifference($type, $fixtureId) {
      $fixture = Fixture::find($fixtureId)->toArray();
      $return = null;
      if(!empty($fixture)) {
         $homeScore = $fixture['home_team_score'];
         $awayScore = $fixture['away_team_score'];
        if($type=='win') {
           if($homeScore >= $awayScore) {
              $diff = $homeScore - $awayScore;
              $string = (string) $diff;
              $return = '+'.$string;
           } else {
        $diff = $awayScore - $homeScore;
        $string = (string) $diff;
        $return = '+'.$string;
           }
        } else {
           if($homeScore >= $awayScore) {
              $diff = $homeScore - $awayScore;
              $string = (string) $diff;
              $return = '-'.$string;
           } else {
        $diff = $awayScore - $homeScore;
        $string = (string) $diff;
        $return = '-'.$string;
           }
        }
      }
      return $return;
    }
   public function updatePointsLML($data) {
    if($data['status']=='complete') {
           if($data['winnerId']==$data['userTeam']) {
             $latest = userStats::where('user_id',$data['user_id'])->orderBy('id', 'DESC')->first();
             $total = 0;
             if(!empty($latest)) {
              $total = $latest->total_points;
             }
             $gdf = $this->calculateGoalDifference('win', $data['fixture_id']);
             $new = new userStats;
             $new->user_id = $data['user_id'];
             $new->debit_point = 0;
             $new->credit_point = 3;
             $new->total_points = $total + 3;
             $new->league_id = $data['league_id'];
             $new->round_id = $data['round_id'];
             $new->fixture_id = $data['fixture_id'];
             $new->goal_difference = $gdf;
             $new->final_goal_difference = $this->finalGD($gdf, $data['league_id'], $data['user_id']);
             $new->save();

          $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
          SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'w']);

          UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'is_team_pickup'=>"no",'last_round_stats' => $stats['old']]);
           $slug = 'win_lml';
             $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id'],$data['round_id']);
             // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
          return true;

           } else {
      $latest = userStats::where('user_id',$data['user_id'])->orderBy('id', 'DESC')->first();
      $total = 0;
      if(!empty($latest)) {
      $total = $latest->total_points;
      }
      $gdf = $this->calculateGoalDifference('loss', $data['fixture_id']);
      $new = new userStats;
      $new->user_id = $data['user_id'];
      $new->debit_point = 0;
      $new->credit_point = 0;
      $new->total_points = $total;
      $new->league_id = $data['league_id'];
      $new->round_id = $data['round_id'];
      $new->fixture_id = $data['fixture_id'];
      $new->goal_difference = $gdf;
            $new->final_goal_difference = $this->finalGD($gdf, $data['league_id'], $data['user_id']);
      $new->save();

      $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'l', $data['round_id']);
      SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'l']);

      UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'is_team_pickup'=>"no",'last_round_stats' => $stats['old']]);
        $slug = 'loss_lml';
      $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
      // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
        return true;
           }
       } else {
              if($data['status']=='draw') {
                 $latest = userStats::where('user_id',$data['user_id'])->orderBy('id', 'DESC')->first();
                 $total = 0;
                 if(!empty($latest)) {
                  $total = $latest->total_points;
                 }
                 $new = new userStats;
                 $new->user_id = $data['user_id'];
                 $new->debit_point = 0;
                 $new->credit_point = 1;
                 $new->total_points = $total + 1;
                 $new->league_id = $data['league_id'];
                 $new->round_id = $data['round_id'];
                 $new->fixture_id = $data['fixture_id'];
                 $new->final_goal_difference = $this->finalGD(0, $data['league_id'], $data['user_id']);
                 $new->save();

                 $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
          SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'w']);

          UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'is_team_pickup'=>"no",'last_round_stats' => $stats['old']]);
          return true;
           }

           if($data['status']=='abandon') {
             $latest = userStats::where('user_id',$data['user_id'])->orderBy('id', 'DESC')->first();
               $total = 0;
               if(!empty($latest)) {
                $total = $latest->total_points;
               }
               $gdf = '+2';
               $new = new userStats;
               $new->user_id = $data['user_id'];
               $new->debit_point = 0;
               $new->credit_point = 3;
               $new->total_points = $total + 3;
               $new->league_id = $data['league_id'];
               $new->round_id = $data['round_id'];
               $new->fixture_id = $data['fixture_id'];
               $new->goal_difference = $gdf;
               $new->final_goal_difference = $this->finalGD($gdf, $data['league_id'], $data['user_id']);
               $new->save();

               $stats = $this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
        SavedTeam::where('user_id',$data['user_id'])->where('league_id', $data['league_id'])->where('round_id',$data['round_id'])->update(['status' => 'w']);

        UsersLeague::where('league_id',$data['league_id'])->where('user_id', $data['user_id'])->where('status','active')->update(['result_stats' => $stats['new'],'is_team_pickup'=>"no",'last_round_stats' => $stats['old']]);
        $slug = 'win_lml';
      $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id'],$data['round_id']);
     // $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
        return true;
           }
       }
   }
    public function ResultLML() {
    $this->LeagueChunkCounter = 30;
    $isReadyToRun = 'no';
    $strRunningProcess = cronProcessLog::where('process_status','running');
    $strRunningProcessCount  = $strRunningProcess->count();

    if($strRunningProcessCount > 1) {
      $isReadyToRun = 'no'; // not be allowed,  need to send developer alerts
      echo '<br/><h1 style="color:red">Not be allowed,  need to send developer alerts </h1>';
      return false;
    }else if($strRunningProcessCount == 0) {
      $isReadyToRun = 'yes'; // case when no process is running
    }

      // case when some other process is running
      if($strRunningProcessCount > 0 && $strRunningProcess->first()->process_name != 'lml_result'){
        $isReadyToRun = 'no';
          return false;
      }

        $status = cronProcessLog::where('process_name','lml_result')->first();

      if($status->process_status=='pending') {
        $isReadyToRun = 'yes';

      }else if($status->process_status=='running') {

        // check last batch if process already running
        if($status->last_batch_status=='complete'){
          $isReadyToRun = 'yes';
        }else if($status->last_batch_status=='running'){
          $isReadyToRun = 'no'; // shouldn't be allow to run

           $checkStatus = $this->cronDelayHandler('lml_result');

	        if($checkStatus!='true') {
	        	echo '<br/>======<h2 style="color:red">Just Wait, we are refreshing the system...</h2>';
	        }
	        return false;
        }
      }

      $lastOneStatus = cronProcessLog::where('process_name','lms_forfeit')->first();
      $lastTwoStatus = cronProcessLog::where('process_name','lml_forfeit')->first();
      $lastThreeStatus = cronProcessLog::where('process_name','lms_result')->first();

      if($isReadyToRun =='yes' && $lastOneStatus->process_status=='complete' && $lastTwoStatus->process_status=='complete' && $lastThreeStatus->process_status=='complete'){
        // mark process running and batch status as running
        cronProcessLog::where('process_name','lml_result')->update(['process_status' => "running", 'last_batch_status' => "running"]);
      }else{
        return false;
      }



        $rid = $lastOneStatus->round_id;
    if($rid) {
        $roundId = $rid;
        $sportId = Round::where('id',$roundId)->value('sport_id');
        $leagues = League::where('current_round_id', $roundId)->where('type','lml')->where('crn_forfeit_checked','yes')->where('crn_result_done','no')->limit($this->LeagueChunkCounter)->get()->toArray();
         if(!empty($leagues)) {
             foreach ($leagues as $key => $value) {
                  $users = $this->getLeagueUserLMSLML($value['id']);
                  if(!empty($users)) {
          foreach ($users as $key => $val) {
            $usrTeam = SavedTeam::where('league_id',$value['id'])->where('round_id',$roundId)->where('user_id',$val)->first();
            if($usrTeam) {
              $fixture = Fixture::find($usrTeam->fixture_id)->toArray();
              $asset = array(
                'userTeam' => $usrTeam->team_id ,
                'userFixture' => $usrTeam->fixture_id,
                'league_id' => $value['id'],
                'round_id' => $roundId,
                'user_id' => $val,
                'status' => $fixture['fixure_result_status'],
                'winnerId' => $fixture['winner_team_id'],
                'fixture_id' => $fixture['id']
              );
              if($this->updatePointsLML($asset)) {
                UsersLeague::where('league_id',$value['id'])->where('user_id',$val)->where('crn_forfeit_checked','yes')->where('crn_result_process','no')->update(['crn_result_process' => "yes"]);
              }
            }
          }
          League::where('id', $value['id'])->where('crn_forfeit_checked','yes')->where('crn_result_done','no')->update(['crn_result_done' => "yes"]);
          $this->updateLeagueRound($value['id'], $roundId);
                  } else {
                    League::where('id', $value['id'])->where('crn_forfeit_checked','yes')->where('crn_result_done','no')->update(['crn_result_done' => "yes"]);
                    $this->updateLeagueRound($value['id'], $roundId);
                  }
             }
             cronProcessLog::where('process_name','lml_result')->update(['last_batch_status' => "complete"]);
       echo '<br/>======<h3 style="color:green">Batch Completed!</h2>';
         } else {
      cronProcessLog::where('process_name','lml_result')->update(['last_batch_status' => "complete",'process_status' => "complete",'round_id' => $roundId]);

      //Round::where('id',$roundId)->update(['result_process_status' => 'complete']);
      $this->markCompleteHandler($roundId);
      echo '<br/>======<h2 style="color:red">No More LML League Found!</h2>';
         }
     }

    }

     public function updateLeagueRound($l_id, $r_id) {
        $getLeague =  League::find($l_id);
        $getAllRound = Round::where('sport_id',$getLeague->sport_id)->orderBy('id')->pluck('id')->toArray();
        $currentRoundId = $getLeague->current_round_id;
        League::where('id',$l_id)->where('round_to_start', $r_id)->where('process_status','pending')->update(['process_status' => 'progress']);
        if(!empty($currentRoundId)) {
          $currentKey = array_search($currentRoundId, $getAllRound);
          $updatedKey = $currentKey + 1;
           if(array_key_exists($updatedKey, $getAllRound)) {
              $nextValue = $getAllRound[$updatedKey];
              League::where('id',$l_id)->update(['current_round_id' => $nextValue,'crn_result_done' => "no", 'crn_forfeit_checked' => "no",'last_round_id' => $r_id]);

              // League::where('id',$l_id)->update(['current_round_id' => $nextValue]);

              return true;
            } else {
              League::where('id',$l_id)->update(['process_status' => 'complete','crn_result_done' => "no", 'crn_forfeit_checked' => "no",'last_round_id' => $r_id]);
              return true;
            }
        } else {
          return false;
        }

       League::where('id', $l_id)->where('crn_forfeit_checked','yes')->where('crn_result_done','yes')->update(['crn_result_done' => "no", 'crn_forfeit_checked' => "no",'crn_undo_done' => "no"]);

   }

  public function mailNotification($slug, $userid, $l_id, $r_id) {
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

   public function markCompleteHandler( $r_id ) {
        $RoundId = Round::find($r_id);
        $getAllRound = Round::where('sport_id',$RoundId->sport_id)->orderBy('id')->pluck('id')->toArray();
        $currentKey = array_search($r_id, $getAllRound);
        $updatedKey = $currentKey + 1;
        if(array_key_exists($updatedKey, $getAllRound)) {
         $nextValue = $getAllRound[$updatedKey];
        } else {
         $nextValue = $r_id;
        }
        $filteredLeague = League::where('current_round_id',$nextValue)->pluck('id')->toArray();
        Round::where('sport_id',$RoundId->sport_id)->update(['is_undu_able' => "no"]);

        Round::where('id',$r_id)->update(['result_process_status' => 'complete','is_undu_able' => "yes"]);
        UsersLeague::whereIn('league_id',$filteredLeague)->where('crn_forfeit_checked','yes')->where('crn_result_process','yes')->update(['crn_result_process' => "no",'crn_forfeit_checked' => "no",'crn_undo_done' => "no"]);
    }



	public function getLeagueUserForUndu($id) {
      return UsersLeague::where('league_id',$id)->where('crn_undo_done','no')->pluck('id')->toArray();
	}
	public function removeUserStats ($rid) {
    	return userStats::where('round_id',$rid)->delete();
    }
    public function updateFixtureUndoStats ( $id ) {
      return Fixture::where('round_id',$id)->update(['fixure_result_status' => "pending"]);
    }
    public function updatePreviousRound ($l_id, $u_id, $r_id,$status) {

	      League::where('id',$l_id)->update(['current_round_id' => $r_id]);
		  League::where('id',$l_id)->where('process_status','complete')->update(['process_status' => 'progress']);


		   UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->where('status','active')->where('is_knockedout','yes')->update(['is_knockedout'=> "no"]);


        UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->where('is_team_pickup','no')->where('is_forfet_knokout','!=','yes')->update(['is_team_pickup'=>"yes"]);
        UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->update(['result_stats' => $status,'crn_undo_done'=> "yes"]);

        Round::where('id',$r_id)->update(['process_status'=>"progress"]);
        SavedTeam::where('user_id',$u_id)->where('league_id',$l_id)->where('round_id',$r_id)->update(['status' => null]);

      return true;
    }
  public function updateLastRoundStats($l_id, $u_id){
    return UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->value('last_round_stats');
  }
   public function undoHandler(){

    $roundId = null;
    $checkRoundExist = Round::where('is_undu_able','running')->orderBy('updated_at','DESC')->first();
    if(!empty($checkRoundExist)) {
    	//Round::where('sport_id',$checkRoundExist->sport_id)->update(['is_undu_able' => "no"]);
    	//Round::where('id',$checkRoundExist->id)->update(['is_undu_able' => "running"]);
    	$roundId = $checkRoundExist->id;
    }

    $isReadyToRun = 'no';
    $strRunningProcess = cronProcessLog::where('process_status','running');
    $strRunningProcessCount  = $strRunningProcess->count();

    if($strRunningProcessCount > 1) {
      $isReadyToRun = 'no'; // not be allowed,  need to send developer alerts
      echo '<br/><h1 style="color:red">Not be allowed,  need to send developer alerts </h1>';
      return false;
    }else if($strRunningProcessCount == 0) {
      $isReadyToRun = 'yes'; // case when no process is running
    }

      // case when some other process is running
      if($strRunningProcessCount > 0 && $strRunningProcess->first()->process_name != 'undu_process'){
        $isReadyToRun = 'no';
          return false;
      }

        $status = cronProcessLog::where('process_name','undu_process')->first();

      if($status->process_status=='pending') {
        $isReadyToRun = 'yes';

      }else if($status->process_status=='running') {

        // check last batch if process already running
        if($status->last_batch_status=='complete'){
          $isReadyToRun = 'yes';
        }else if($status->last_batch_status=='running'){
          $isReadyToRun = 'no'; // shouldn't be allow to run

           $checkStatus = $this->cronDelayHandler('undu_process');

	        if($checkStatus!='true') {
	        	echo '<br/>======<h2 style="color:red">Just Wait, we are refreshing the system...</h2>';
	        }
	        return false;
        }
      }

      $lastOneStatus = cronProcessLog::where('process_name','lms_forfeit')->first();
      $lastTwoStatus = cronProcessLog::where('process_name','lml_forfeit')->first();
      $lastThreeStatus = cronProcessLog::where('process_name','lms_result')->first();
      $lastFourStatus = cronProcessLog::where('process_name','lml_result')->first();

      if($isReadyToRun =='yes' && $lastOneStatus->process_status=='complete' && $lastTwoStatus->process_status=='complete' && $lastThreeStatus->process_status=='complete' && $lastFourStatus->process_status=='complete' && !is_null($roundId)){
        // mark process running and batch status as running
        cronProcessLog::where('process_name','undu_process')->update(['process_status' => "running", 'last_batch_status' => "running"]);
      }else{

        return false;
      }





    // $minimunLeagueProcess = $this->LeagueChunkCounter;
    $minimunLeagueProcess = 50;

   	$getAllLeagueByRound = League::where('last_round_id',$roundId)->where('crn_undo_done','no')->limit($minimunLeagueProcess)->pluck('id')->toArray();
   	  if(!empty($getAllLeagueByRound)) {
	       foreach ($getAllLeagueByRound as $key => $value) {
	   		  $leagueUser = $this->getLeagueUserForUndu($value);

	   		  if(!empty($leagueUser)) {
                foreach ($leagueUser as $ke => $val) {
                  //$this->removeUserStats($value, $roundId, $val);
                  $status = $this->updateLastRoundStats($value, $val);
                $this->updatePreviousRound($value, $val, $roundId,$status);
                }

	   		  }
	   		  League::where('id',$value)->update(['crn_undo_done' => "yes"]);
	   	   }
	   	   cronProcessLog::where('process_name','undu_process')->update(['last_batch_status' => "complete"]);
	   	   echo "Batch Completed";
   	  } else {
   	  	$this->updateFixtureUndoStats($roundId);
   	  	 $this->removeUserStats($roundId);
   	  	cronProcessLog::where('process_name','undu_process')->update(['process_status' => "complete", 'last_batch_status' => "complete"]);
   	  	Round::where('id',$roundId)->update(['is_undu_able' => "no",'result_process_status' => "pending"]);
   	  	League::where('last_round_id',$roundId)->update(['crn_undo_done' => "no"]);
   	  	$all = League::where('last_round_id',$roundId)->pluck('id')->toArray();
   	  	UsersLeague::whereIn('league_id',$all)->update(['crn_undo_done' => "no"]);
   	  	echo "No league Found";

   	  }

   }

   public function getUserForWinnerCalculation($id) {
   	return UsersLeague::where('league_id', $id)->get()->toArray();
   }
   public function handleLMLNotification($l_id, $r_id) {
    return userStats::where('league_id', $l_id)->where('round_id', $r_id)->get()->toArray();
   }
   public function saveWinnerSummary($data) {
     leagueWinner::create($data);
   }
   public function leagueWinnerHandler() {
   	$round = Round::where('result_process_status','complete')->where('is_undu_able','!=','running')->where('notification_status','!=','complete')->first();
   	if(empty($round)) {
   		echo "No Round Found!";
   		return false;
   	} else {
   		$roundId = $round->id;
   		Round::where('id',$roundId)->where('notification_status','pending')->update(['notification_status'=> 'running']);
   	}

   	$league = League::where('last_round_id',$roundId)->where('is_winner_calculated','no')->limit($this->LeagueChunkCounter)->get()->toArray();
   	if(!empty($league)) {
       foreach ($league as $key => $value) {
       	 if($value['type']=='lms') {
       	    $leagueUser = $this->getUserForWinnerCalculation($value['id']);
	       	 if(!empty($leagueUser)) {
	           foreach ($leagueUser as $k => $val) {
	           	if($val['is_knockedout']=='yes') {
	           	  $save = array(
	           	     'league_id' => $value['id'],
	           	     'round_id' => $roundId,
	           	     'user_id' =>  $val['user_id'],
	           	     'league_type' => $value['type'],
	           	     'status' => 'l'
	           	  );
	           	  $this->saveWinnerSummary($save);
	           	} else {
	              $save = array(
	           	     'league_id' => $value['id'],
	           	     'round_id' => $roundId,
	           	     'user_id' =>  $val['user_id'],
	           	     'league_type' => $value['type'],
	           	     'status' => 'w'
	           	  );
	           	  $this->saveWinnerSummary($save);
	           	}
	           }
	       	 } else {
	       	 	echo "No User Found";
	       	 }
	      } else {
            $lmlStats = $this->handleLMLNotification($value['id'], $roundId);
            if(!empty($lmlStats)) {
              foreach ($lmlStats as $ke => $v) {
                if($v['credit_point']== 0) {
                  $save = array(
	           	     'league_id' => $value['id'],
	           	     'round_id' => $roundId,
	           	     'user_id' =>  $v['user_id'],
	           	     'league_type' => $value['type'],
	           	     'status' => 'l'
	           	  );
	           	  $this->saveWinnerSummary($save);
                }
                if($v['credit_point']== 3) {
                  $save = array(
	           	     'league_id' => $value['id'],
	           	     'round_id' => $roundId,
	           	     'user_id' =>  $v['user_id'],
	           	     'league_type' => $value['type'],
	           	     'status' => 'w'
	           	  );
	           	  $this->saveWinnerSummary($save);
                }
                if($v['credit_point']== 1) {
                  $save = array(
	           	     'league_id' => $value['id'],
	           	     'round_id' => $roundId,
	           	     'user_id' =>  $v['user_id'],
	           	     'league_type' => $value['type'],
	           	     'status' => 'd'
	           	  );
	           	  $this->saveWinnerSummary($save);
                }
              }
            } else {
             echo "No LML Stats Found ";
            }
	      }
	     League::where('id',$value['id'])->update(['is_winner_calculated'=> "yes"]);
       }
       echo "Batch Completed";

   	} else {
   		echo "No League Found";
   		Round::where('id',$roundId)->update(['notification_status'=> 'complete']);
   		League::where('last_round_id',$roundId)->update(['is_winner_calculated'=> "no"]);
   	}
   }

}
