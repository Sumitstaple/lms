<?php

namespace Modules\Frontend\Http\Controllers;

use App\Jobs\SendWelcomeEmail;
use Illuminate\Http\Request;
use Response;
use Illuminate\Routing\Controller;
use Modules\Frontend\Models\Invite;
use Mail;
use App\User;
use Auth;
use Modules\Frontend\Models\League;
use Modules\Frontend\Models\LeagueLocation;
use Modules\Frontend\Models\Round;
use Modules\Frontend\Models\Team;
use Modules\Sport\Models\Fixture;
use Modules\Result\Models\userStats;
use Modules\Frontend\Models\SavedTeam;
use Modules\Frontend\Models\UsersLeague;
use App\Repositories\SportRepository;
use App\Repositories\TeamSelection;
use App\Laravue\JsonResponse;
use App\Helper\SystemLogs;



class LeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(League $League, LeagueLocation $LeagueLocation, Round $round)
   {
       $this->model = new SportRepository($League);
       $this->round = new TeamSelection($round);
   }

    public function sendInvite(Request $request)
    {

        if($request->has('desc') && !empty($request->desc)){
        	$explode=  array();
          if (strpos(trim($request->desc), ',') !== false) {
          	$allEmail = explode(',', trim($request->desc));
            $allEmail = array_unique($allEmail);
            $allEmailChecked = false;
          	foreach($allEmail as $emailL) {
          		    $email = trim( $emailL );
    			 if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                  $allEmailChecked= false;
                  return Response::json(['message' => 'Invalid email format', 'status' => 'failed']);
    			 }else{
                   $allEmailChecked= true;
                   $this->mailHandler($email, $request->leagueid);
    			    }
			    }
          if($allEmailChecked) {
            return Response::json(['message' => 'Invitation has been sent!', 'status' => 'success']);
          }
          } else {
           if (!filter_var(trim($request->desc), FILTER_VALIDATE_EMAIL)) {
                return Response::json(['message' => 'Invalid email format', 'status' => 'failed']);
            } else {
                $this->mailHandler(trim($request->desc), $request->leagueid);
                return Response::json(['message' => 'Invitation has been sent!', 'status' => 'success']);
            }
          }
          // echo "<pre>";
          // print_r($explode);
          // die;
        }
        else{
           return Response::json(['message' => 'Please enter the emails!', 'status' => 'failed']);
        }

    }

    public function mailHandler($to, $leaugeId) {
      // $subject= 'Leauge Invitation for League id:-'.$leaugeId;
      $subject= 'You have been invited!';
      $mode = 'system';
      $league_name = League::where('id',$leaugeId)->value('league_name');
      
      if (User::where('email',$to)->count() > 0) {
          $invite = $this->savetoInvite($to, $mode, $leaugeId);
          if($invite) {
            $invite = base64_encode($invite);
            $leaugeId = base64_encode($leaugeId);
            $link = url("/#/").'login?lms='.$leaugeId.'&id='.$invite.'&mode='.$mode;
            $data = array();
            $data['link'] = $link;
            $data['league_name'] = $league_name;
            SendWelcomeEmail::dispatch($to, $subject, $data)->onQueue('emails');
          }
      } else {
        $invite = $this->savetoInvite($to, $mode, $leaugeId);
        $leaugeId = base64_encode($leaugeId);
        if($invite) {
          $invite = base64_encode($invite);
          $link = url("/#/").'register?lms='.$leaugeId.'&id='.$invite.'&mode='.$mode;
          $data = array();
          $data['link'] = $link;
          $data['league_name'] = $league_name;
          SendWelcomeEmail::dispatch($to, $subject, $data)->onQueue('emails');
        }
      }
    }
    public function savetoInvite($to, $mode, $lid) {
      $new = new Invite;
      $new->email = $to;
      $new->mode = $mode;
      $new->added_by = Auth::id();
      $new->leagues_id = $lid;
      $new->invitation_status = 'sent';
      $new->save();
      return $new->id;
    }

     /**
     * get leauge info.
     * @param int $id
     * @return Response
     */
    public function getLeague($id) {
      $id = base64_decode($id);
      $leauge = $this->model->get($id);
      if($leauge) {
          $leauge_name = $leauge->league_name;
          return response()->json(new JsonResponse(['status' => 'success','leauge_name' => $leauge_name]));
      } else {
          return response()->json(new JsonResponse(['status' => 'error']));
      }
    }

    public function getRounds($id) {
      $data = League::where('id',$id)->where('status','active')->first()->toArray();
      if($data) {
         $endDate = $data['end_datetime'];
         $now = date('Y-m-d H:i:s');
         $rounds = Round::where('id',$data['current_round_id'])->where('start_datetime','>=',$now)->get()->toArray();
         if(!empty($rounds)) {
         	  return response()->json(new JsonResponse(['status' => 'success','data' => $rounds]));	
         	} else {
         		return response()->json(new JsonResponse(['status' => 'failed','message' => 'No Round available for this League!']));
         	}
      }else{
      	return response()->json(new JsonResponse(['status' => 'failed','message' => 'No League Found!']));
      }
    }

    public function getTeams($r_id, $s_id, $l_id) { 
    	if(Fixture::where('sport_id',$s_id)->where('round_id',$r_id)->where('status','active')->count() == 0) {
    		 return response()->json(new JsonResponse(['status' => 'error','message' => 'Fixtures have not yet been uploaded for this round']));
    	} 

    	$isKnockedOut = UsersLeague::where('league_id',$l_id)->where('user_id',Auth::id())->value('is_knockedout');
    	if( $isKnockedOut!='yes' ) {
    	  $round = Round::find($r_id);	
          $roundStartTime = date("Y-m-d H:i:s",strtotime("-15 minutes", strtotime($round->start_datetime)));
          $now = date('Y-m-d H:i:s');
          if(strtotime($roundStartTime) >= strtotime($now)) {

          	   $filterFixture = $this->round->getTeamtoSelect($r_id, $s_id, $l_id);

          	   $selectedTeam = SavedTeam::where('user_id',Auth::id())->where('league_id',$l_id)->where('round_id',$r_id)->first();
               $prtd = 0;
               if(!empty($selectedTeam)) {
               	$selectedTeam = $selectedTeam->fixture_id.'-'.$selectedTeam->team_id;
               	$prtd = 1;
               } else {
               	$selectedTeam = null;
               }
              return response()->json(new JsonResponse(['status' => 'success','data' => $filterFixture,'selectedTeam' => $selectedTeam]));
          }else{
             return response()->json(new JsonResponse(['status' => 'failed','message' => 'Opps!, You are too late to join this Round!']));
          }
    	} else {
           return response()->json(new JsonResponse(['status' => 'failed','message' => 'Opps!, You are already knocked out from this League!']));
    	}
    }
    


    public function saveTeams($r_id, $l_id, $t_id) {
        $round = Round::find($r_id);
        if( $round->process_status=='pending' ) {
	      $roundStartTime = date("Y-m-d H:i:s",strtotime("-15 minutes", strtotime($round->start_datetime)));
	      $now = date('Y-m-d H:i:s');
	      if(strtotime($roundStartTime) >= strtotime($now)) {
  			$explode = explode('-',$t_id);
  			$t_id = (int) $explode[1];
  			$f_id = (int) $explode[0];

	    	$updateornew = SavedTeam::updateOrCreate(
			    ['user_id' => Auth::id(),'league_id' => $l_id, 'round_id' => $r_id],
			    ['team_id' => $t_id,'fixture_id'=> $f_id]
			);

			if($updateornew->save()){
				UsersLeague::where('league_id',$l_id)->where('user_id',Auth::id())->where('is_team_pickup','no')->update(['is_team_pickup'=>"yes"]);
			$getteam = Team::where('id',$t_id)->first();	
			$log_action = "Team Picked";
            $log_type = 6;
            $description = Auth::user()->first_name." ".Auth::user()->last_name." picked".$getteam->team_name." for League (ID::".$l_id.")";
            SystemLogs::GenerateLogs($log_action,$description,$log_type);	
        // print_r($getteam->team_name); die;
				return response()->json(new JsonResponse(['status' => 'success','message' => $getteam->team_name.' are locked in']));
			}
		} else {
			return response()->json(new JsonResponse(['status' => 'failed','message' => 'Opps!, Round is already Started!']));
		  }
	   } else {
	   	 return response()->json(new JsonResponse(['status' => 'failed','message' => 'Opps!, Either Round has started or Result has Processed!, You can not change the Team']));
	   }

   }
    /**
     * Search leauges.
     * @param int $id
     * @return Response
     */

    public function findleaguelist(Request $request) {

      $check = "no";
     
      if(($request->title != '' || $request->sport_id != '' || $request->type != '') || ($request->league_city != '' && $request->league_state != '' && $request->league_country != '')) {
           $keyword = trim($request->title);
           $location_ids=null;
           $leaugeLo = LeagueLocation::where('league_city',$request->league_city)->where('league_state',$request->league_state)->where('league_country',$request->league_country)->pluck('league_id');
        if($request->league_country == "empty"){
          $location_ids = $leaugeLo;
        }   
        elseif(count($leaugeLo) > 0) {
         
            $location_ids = $leaugeLo;
        }
        else{
          $location_ids = array('0');
        }


           $leauge = $this->model->findpaginateWithsearch($request->limit, $request->page,array('sport','round','location'),$request->sport_id,$keyword,$request->type,$location_ids);
           $check = "yes";
      }

      if($check == "no"){

      $leauge = $this->model->findpaginateWith($request->limit, $request->page,array('sport','round','location'));
      }



      return response()->json(new JsonResponse(['items' => $leauge, 'total' => 2]));
    }  

    public function search(Request $request) {

      $leauge = League::whereIn('is_private',array('no','yes'));
      if($request->title != '') {
         $keyword = trim($request->title);
           $leauge = $leauge->where('league_name', 'like', "%{$keyword}%");
      }

      if($request->sport_id != '') {
           $leauge = $leauge->where('sport_id', $request->sport_id);
      }

       if($request->type != '') {
           $leauge = $leauge->where('type', $request->type);
      }

      if($request->league_city != '' && $request->league_state != '' && $request->league_country != '') {
        $leaugeLo = LeagueLocation::where('league_city',$request->league_city)->where('league_state',$request->league_state)->where('league_country',$request->league_country)->pluck('league_id');
        if($leaugeLo) {
            $leauge = $leauge->whereIn('id', $leaugeLo);
        } 
      }

      $leauge = $leauge->with('sport','round')->with('location')->get()->toArray();
      
      $arr = array('data' => $leauge);

      return response()->json(new JsonResponse(['items' => $arr, 'total' => 2]));
    }

     /**
     * View leauge.
     * @param int $id
     * @return Response
     */
     public function calculateCount($str, $type) {
       $return  = 0;
       if($type=='win') {
         if(isset($str) || trim($str)!='') {
           $return = substr_count($str, "w");
         }	
       }
       if($type=='loss') {
         if(isset($str) || trim($str)!='') {
           $return = substr_count($str, "l");
         }	
       }
       if($type=='draw') {
         if(isset($str) || trim($str)!='') {
           $return = substr_count($str, "d");
         }	
       }
       return $return;
     }
     public function last5roundResult($str) {
       $return = array();
       if(!empty($str)) {
       	$removeComma = str_replace(",","",$str);
       	 if(strlen($removeComma) <= 5) {
          $return = explode(',', $str);
       	 } else {
             $lastFive = substr($removeComma ,-5);
             $return = str_split($lastFive, "1");
       	 }
       }
       return $return;
     }
    public function show($id) {
      $leauge = $this->model->withGet('leagueusers',$id)->toArray();
     
      $response = array();
      	foreach ($leauge['leagueusers'] as $k => $val) {
      		$countW = (int) $this->calculateCount(trim($val['result_stats']), 'win');
      		$countL = (int) $this->calculateCount(trim($val['result_stats']), 'loss');
      		$countD = (int) $this->calculateCount(trim($val['result_stats']), 'draw'); 
      		$countT = $countW + $countL + $countD;
      		$latest = userStats::where('user_id',$val['user_id'])->where('league_id',$leauge['id'])->orderBy('id', 'DESC')->first();
             $total = '0';
             if(!empty($latest)) { 
             	$total = $latest->final_goal_difference;
             }
          $counts = array_count_values($this->last5roundResult(trim($val['result_stats'])));
          if(isset($counts['w']) ){
            $roundcount = $counts['w'];  
          }
          else{
              $roundcount = 0;
            }
      		$response[] = array(
               'userName' => ucfirst($val['user']['first_name']).' '.ucfirst($val['user']['last_name']) ,
               'nickname' => ucfirst($val['user']['nickname']),
               'win' => $countW,
               'draw' => $countD,
               'loss' => $countL,
               'played' => $countT,
               'goalDifference' => $total,
               'last5roundResult' => array_reverse($this->last5roundResult(trim($val['result_stats']))),
               'roundcount'=>$roundcount
      		);  
          
      	}
          // print_r($response); die;
          usort($response, function($a, $b) {
              return $a['roundcount'] <=> $b['roundcount'];
          });
          
          return response()->json(new JsonResponse(['items' => array_reverse($response)]));
    }

   public function leagueinfo($id) {
      $leauge = $this->model->withGet('leagueusers',$id)->toArray();

      $leagueuser = UsersLeague::where([['league_id',$id],['user_id',Auth::id()],['status','active']])->first();
      if($leagueuser){
        $leauge['league_member'] = 'yes';
      }
      else{
        $leauge['league_member'] = 'no';
      }
      return response()->json(new JsonResponse(['leagueinfo' => $leauge]));
    }
}
