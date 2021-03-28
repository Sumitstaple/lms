<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Modules\Frontend\Models\League;
use Carbon\Carbon;
use Response;
use Modules\Frontend\Models\UsersLeague;
use Modules\Frontend\Models\SavedTeam;
use Modules\Sport\Models\LeagueLocation;
use Modules\Sport\Models\Team;
use Modules\Sport\Models\Fixture;
use App\User;
use Hash;
use App\Laravue\Models\PermissionGroup;

use Modules\Sport\Models\ManageTeams;
/**
 * Class LaravueController
 *
 * @package App\Http\Controllers
 */
class LaravueController extends Controller
{
    /**
     * Entry point for Laravue Dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('laravue');
    }

    public function createleague($user_id,$sport_id,$round_id,$if_forfeit){
		set_time_limit(20000000);
		$columns = 2000;
      $adminUserID = 1;
    	for ($k = 1 ; $k <= $columns; $k++){
	    	$league = new League();
	    	$league->user_id = $adminUserID;
	    	$league->league_name = 'Australian Football League-'.$k;

            if($k > 1001){
                if($k % 2 == 0){ 
                    $league->type = 'lml';
                    $league->if_forfeit = 'assign_lowest_team';
                }
                else{
                    $league->type = 'lms';
                    $league->if_forfeit = 'assign_lowest_team';
                }
            }
            else{
              
    	    	if($k % 2 == 0){ 
    	    		$league->type = 'lml';
                    $league->if_forfeit = 'knocked_out';
    	    	}
    	    	else{
    	    		$league->type = 'lms';
                    $league->if_forfeit = 'knocked_out';
    	    	}
            }
	    	$league->sport_id = $sport_id;
	    	$league->round_to_start = $round_id;
	    	$league->is_private = 'no';
	    	$league->current_round_id = $round_id;
	    	$league->is_banterboard = 'yes';
	    	$league->start_datetime = date("Y-m-d h:i:s");
        $league->end_datetime = date("Y-m-d h:i:s");

	    	$league->save();

	    	$locationData = new LeagueLocation();
            $locationData->league_id = $league->id;
            $locationData->league_town = '';
            $locationData->league_city = 'Acacia Gardens';
            $locationData->league_state = 'NSW';
            $locationData->league_country = 'Australia';
            // print_r($locationData); die;
            if($locationData->save()){

                // $leagueUsers = new UsersLeague();
                // $leagueUsers->league_id=$league->id;
                // $leagueUsers->user_id = $user_id;
                // $leagueUsers->sport_id = $sport_id;
                // $leagueUsers->is_admin = 'yes';
                // $leagueUsers->is_team_pickup = 'no';
                // $leagueUsers->is_knockedout = 'no';
                // $leagueUsers->is_play = 'no';
                // $leagueUsers->status='active';
                // $leagueUsers->save();
            }
        $adminUserID = $adminUserID +10;
      }


        /*$checkteams = ManageTeams::where('sport_id',$sport_id)->get();

        if(count($checkteams) == 0){
            $getteams = Team::where('sport_id',$sport_id)->get();
            
            foreach ($getteams as $key => $value) {

              $manageteams = new ManageTeams();

              $manageteams->sport_id = $sport_id;
              $manageteams->team_id = $value->id;
              if($value->id == 2){
                $manageteams->rank_order = 6;
              }
              elseif($value->id == 6){
                $manageteams->rank_order = 2;
              }
              else{
                $manageteams->rank_order = $key+1;
              }
              
              $manageteams->save();
            }
        }*/




    }

    public function joinleagues($sport_id){
    set_time_limit(20000000);

    $columns = 10;	
    
    $leagues = League::all();

    $current_users = 0;

    foreach ($leagues as $key => $leaguevalue) {
      $strLeagueID = $leaguevalue->id;
      $strStartUserId = (($strLeagueID*10)-9);
      $strEndUserId = ($strLeagueID*10);

      for($uid=$strStartUserId; $uid<=$strEndUserId; $uid++){
          $leagueUsers = new UsersLeague();
          $leagueUsers->league_id=$leaguevalue->id;
          $leagueUsers->user_id = $uid;
          $leagueUsers->sport_id = $sport_id;
          $leagueUsers->is_admin = 'no';
          $leagueUsers->is_team_pickup = 'no';
          $leagueUsers->is_knockedout = 'no';
          $leagueUsers->is_play = 'no';
          $leagueUsers->status='active';
          $leagueUsers->save();
                
      }
    }

    return;
      

    }


public function createusers(){

	    $passwordStr = '123456789';
        $password = Hash::make($passwordStr);
        // $Getpermission = '2';
        $permission_ids = '2';
        $usercount = 5000;
        $total = 20000;
        $s = 1;
        $users = array();
        while($usercount <= $total) {
          $users = array();
          for ($u=$s; $u <= $usercount; $u++) {
              $data = array();
              $data['first_name'] = 'user '.$u;
              $data['last_name'] = 'jhon';
              $data['email'] = 'league_user'.$u.'@yopmail.com';
              $data['password'] = $password;
              $data['access_permissions'] = $permission_ids;
              $data['role_id'] = 2;
              $data['is_mark_admin'] = 'no';
              $users[] = $data;
          }
          $s = $u;
          $usercount = $usercount+5000;
          User::insert($users);
        }
        echo $total.' user has been created.';
    }


public function teamAllocation($team_1, $team_2, $fixture1, $fixture2) {
    set_time_limit(20000000);
	if($team_1!='' && $team_2!='' && $fixture1!='' && $fixture2!='') {
     $userLeague = UsersLeague::where('is_knockedout','no')->get();
     $check = 1;
     //$isTeamSkip = 'no';
    $user_check = true;
     foreach ($userLeague as $key => $value) {

        if($user_check == true){

            if($check>0) {
                $roundId = League::find($value->league_id)->current_round_id;

                $updateornew = SavedTeam::updateOrCreate(
          ['user_id' => $value->user_id,'league_id' => $value->league_id, 'round_id' => $roundId],
          ['team_id' => $team_2,'fixture_id'=> $fixture2]
      );

                // $new = new SavedTeam;
                // $new->user_id = $value->user_id;
                // $new->league_id = $value->league_id;
                // $new->round_id = $roundId;
                // $new->team_id = $team_2;
                // $new->fixture_id = $fixture2;
                // $new->save();
                --$check;
                echo "UserId-> ".$value->user_id." locked with Team".$team_2."<br>";
            } else {
                $roundId = League::find($value->league_id)->current_round_id;

                $updateornew = SavedTeam::updateOrCreate(
          ['user_id' => $value->user_id,'league_id' => $value->league_id, 'round_id' => $roundId],
          ['team_id' => $team_1,'fixture_id'=> $fixture1]
      );

                // $new = new SavedTeam;
                // $new->user_id = $value->user_id;
                // $new->league_id = $value->league_id;
                // $new->round_id = $roundId;
                // $new->team_id = $team_1;
                // $new->fixture_id = $fixture1;
                // $new->save();
                ++$check;
                echo "UserId-> ".$value->user_id." locked with Team ".$team_1."<br>";
            }
            $user_check = false;
            // echo $value->user_id;
            UsersLeague::where('league_id',$value->league_id)->where('user_id',$value->user_id)->where('is_team_pickup','no')->update(['is_team_pickup'=>"yes"]);
        }
        else{
            $user_check = true;
        }

        
        

     }
    } else {
        echo "all fields need to fill";
    }
    die();
    
    echo $userCount."<br>";
    echo $team_1."<br>";
    echo $team_2."<br>";
    die();
}

public function teamsAllocation(Request $request) {
    set_time_limit(20000000);

    // print_r($request->all()); die;

 
     $userLeague = UsersLeague::where('is_knockedout','no')->get();

     foreach ($userLeague as $key => $value) {

                $user_last_name = User::where('id',$value->user_id)->value('last_name');
                $roundId = League::find($value->league_id)->current_round_id;

                if($user_last_name == 'type2'){

                  $updateornew = SavedTeam::updateOrCreate(
                      ['user_id' => $value->user_id,'league_id' => $value->league_id, 'round_id' => $roundId],
                      ['team_id' => $request->team2_id,'fixture_id'=> $request->fixture2_id]
                  );
                   UsersLeague::where('league_id',$value->league_id)->where('user_id',$value->user_id)->where('is_team_pickup','no')->update(['is_team_pickup'=>"yes"]);
                  echo "UserId-> ".$value->user_id." locked with Team".$request->team2_id."<br>";
                }

                elseif($user_last_name == 'type1'){
                      $updateornew = SavedTeam::updateOrCreate(
                          ['user_id' => $value->user_id,'league_id' => $value->league_id, 'round_id' => $roundId],
                          ['team_id' => $request->team1_id,'fixture_id'=> $request->fixture1_id]
                      );
                       UsersLeague::where('league_id',$value->league_id)->where('user_id',$value->user_id)->where('is_team_pickup','no')->update(['is_team_pickup'=>"yes"]);
                      echo "UserId-> ".$value->user_id." locked with Team ".$request->team1_id."<br>";
                }
                else{
                  echo "type3 or type4";
                }    

     }

}

public function assignleaguethroughadmin($team1, $team2) {

    set_time_limit(20000000);

     $userLeague = UsersLeague::where('is_knockedout','no')->get();

     foreach ($userLeague as $key => $value) {

                $user_last_name = User::where('id',$value->user_id)->value('last_name');
                $roundId = League::find($value->league_id)->current_round_id;

                $fixture1 = Fixture::where([
                    ['round_id', '=', $roundId],
                    ['home_team_id', '=', $team1]
                ])->orWhere([
                    ['round_id', '=', $roundId],
                    ['away_team_id', '=', $team1]
                ])->value('id');

                $fixture2 = Fixture::where([
                    ['round_id', '=', $roundId],
                    ['home_team_id', '=', $team2]
                ])->orWhere([
                    ['round_id', '=', $roundId],
                    ['away_team_id', '=', $team2]
                ])->value('id');

                if($user_last_name == 'type2'){

                  $updateornew = SavedTeam::updateOrCreate(
                      ['user_id' => $value->user_id,'league_id' => $value->league_id, 'round_id' => $roundId],
                      ['team_id' => $team2,'fixture_id'=> $fixture2]
                  );
                   UsersLeague::where('league_id',$value->league_id)->where('user_id',$value->user_id)->where('is_team_pickup','no')->update(['is_team_pickup'=>"yes"]);
                   echo "UserId-> ".$value->user_id." locked with Team".$request->team2_id."<br>";
                }

                elseif($user_last_name == 'type1'){
                      $updateornew = SavedTeam::updateOrCreate(
                          ['user_id' => $value->user_id,'league_id' => $value->league_id, 'round_id' => $roundId],
                          ['team_id' => $team1,'fixture_id'=> $fixture1]
                      );
                       UsersLeague::where('league_id',$value->league_id)->where('user_id',$value->user_id)->where('is_team_pickup','no')->update(['is_team_pickup'=>"yes"]);
                      echo "UserId-> ".$value->user_id." locked with Team ".$request->team1_id."<br>";
                }
                
                else{
                  echo "type3 or type4";
                }    

     }

    return Response::json(['message' => 'You have succesfully assigned the team!', 'status' => 'success']);

}

}
