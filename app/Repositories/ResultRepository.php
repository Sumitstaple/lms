<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;
use Modules\Result\Models\userStats;
use Modules\Frontend\Models\UsersLeague;
use Modules\Sport\Models\League;
use Modules\Sport\Models\Round;
use Modules\Sport\Models\ManageTeams;
use Modules\Sport\Models\Fixture;
use Modules\Sport\Models\Team;
use App\Helper\NotificationHandler;
use Modules\Frontend\Models\SavedTeam;
use Modules\Sport\Models\NotificationTemplate;
use Modules\Result\Models\mailNotification;
use Auth;
use App\User;


class ResultRepository implements ResultRepositoryInterface
{

     // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    } 

    /**
     * Get's a post by it's ID
     *
     * @param int
     * @return collection
     */
    public function get($post_id)
    {
        return $this->model->find($post_id);
    }

    /**
     * Get's all posts.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get's first posts.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->model->first();
    }

    /**
     * Deletes a post.
     *
     * @param int
     */
    public function delete($post_id)
    {
    }

    /**
     * Updates a post.
     *
     * @param int
     * @param array
     */
    public function update($post_id, array $post_data)
    {
        return $this->model->find($post_id)->update($post_data);
    }

    public function updatenew(array $post_id, array $post_data)
    {
        return $this->model->where($post_id)->update($post_data);
    }

    /**
     * Updates a post.
     *
     * @param int
     * @param array
     */

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    //  public function with($relations)
    // {
    //     return $this->model->with($relations);
    // }
    public function with($relations=array())
    {
        $this->model->with($relations);
    }

    public function where($sport_id)
    {
        return $this->model->where('sport_id',$sport_id);
    }

    /**
     * Get's pagination.
     *
     * @return mixed
     */
    public function paginate($limit,$pageNumber)
    {
        return $this->model->paginate($limit, ['*'], 'page', $pageNumber);
    }
     public function paginateWithSearch($limit,$pageNumber,$relations=array(),$str)
    {
        return $this->model->with($relations)->where('sport_id',$str)->orderBy('end_datetime','ASC')->paginate($limit, ['*'], 'page', $pageNumber);
    }

     public function paginateWith($limit,$pageNumber,$relations=array())
    {
        return $this->model->with($relations)->orderBy('end_datetime','ASC')->paginate($limit, ['*'], 'page', $pageNumber);
    }
    public function getUniquRound() {
    	return $this->model->select('id','sport_name')->distinct()->get()->toArray();
    }

    public function getallRounds()
    {
        $now = date('Y-m-d H:i:s');
        //where('start_datetime','>=',$now)->where('end_datetime','<=',$now)
        return $this->model->with('sport')->orderBy('end_datetime','ASC')->get()->toArray();
    }

    public function getfixture($id, $s_id) {
    	return $this->model->with('hometeam')->with('awayteam')->where('round_id',$id)->where('sport_id',$s_id)->get()->toArray();
    }
    public function checkRoundUnduable($id) {
    	return $this->model->where('id',$id)->value('is_undu_able');
    }
    public function settoAbondon($id) {
    	return $this->model->where('id',$id)->update(['fixure_result_status' => 'abandon']);
    }

    public function saveResult(array $data) {
      if($this->savetoFixture($data)) {
      	return true;
      }
    }
    public function getSportId( $id ) {
    	return $this->model->where('id',$id)->value('sport_id');
    }

    // Save all fixture into lms_fixture table with winner team and looser team id
    public function savetoFixture(array $data) {
       foreach ($data as $key => $value) {
       	 if($value['status']=='completed') {
       	 	$team = $this->model->find($value['fixture_id'])->toArray();  
           if($value['winner']=='away') {
             $winnerId = $team['away_team_id'];
             $looserId = $team['home_team_id'];
             $awayTeamScore = $value['winnerPoint'];
             $homeTeamScore = $value['looserPoint'];
           } else {
           	 $looserId = $team['away_team_id'];
             $winnerId = $team['home_team_id'];
             $awayTeamScore = $value['looserPoint'];
             $homeTeamScore = $value['winnerPoint'];
           }
           $this->model->where('id',$value['fixture_id'])->update(['winner_team_id' => $winnerId,'looser_team_id' => $looserId,'fixure_result_status' => 'complete','home_team_score' => $homeTeamScore,'away_team_score' => $awayTeamScore]);
       	 } else {
				$awayTeamScore = $value['winnerPoint'];
				$homeTeamScore = $value['looserPoint'];
       	 	$this->model->where('id',$value['fixture_id'])->update(['fixure_result_status' => 'draw','home_team_score' => $homeTeamScore,'away_team_score' => $awayTeamScore]);
       	 }
       }
       return true;
    }

    public function getLeagueList($sportID, $roundID) {
    	return $this->model->where('sport_id',$sportID)->where('current_round_id',$roundID)->where('status','active')->get()->toArray();
    }

    public function getLeagueUser( $league ) {
       $return = array(); 
       foreach ($league as $key => $value) {
       	 $return[] = array(
            'league_id' => $value['id'] ,
            'league_type' => $value['type'] ,
            'user_id' => $this->model->where('league_id',$value['id'])->where('is_knockedout','no')->where('status','active')->pluck('user_id')->toArray()
       	 );	
       	}
       	return $return;	
    }
     
    public function filterWinner ($status, $key) {
    	$winnerId = null;
        $return = array(); 
        $explode = explode('_', $key);
    	if($status=='completed') {
    		$winnerId = $this->model->where('id',$explode[1])->where('status','active')->value('winner_team_id');
    		$return = array(
               'winnerId' => $winnerId,
               'status' => 'completed',
               'fixture_id' => $explode[1]
    		);
    	} else {
    		$return = array(
               'winnerId' => $winnerId,
               'status' => 'draw',
               'fixture_id' => $explode[1]
    		);
    	}
    	return $return;
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
    public function updatePointsLML( $data ) {
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
             $this->updateRoundStats($data['league_id'], $data['user_id'], 'w',$data['round_id']);
             $slug = 'win_lml';
             $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id'],$data['round_id']);
             $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
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
					$this->updateRoundStats($data['league_id'], $data['user_id'], 'l', $data['round_id']);
				    $slug = 'loss_lml';
				    $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
				    $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
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
		             $this->updateRoundStats($data['league_id'], $data['user_id'], 'd', $data['round_id']);
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
	             $this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
	             $slug = 'win_lml';
	             $this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id'],$data['round_id']);
	             $this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
	         }
       }

        if($this->updateknockedOut($data['league_id'], $data['user_id'])) {
          	return true;
          } else {
          	return false;
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

    public function updateknockedOut ($l_id, $u_id) {
      UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->where('is_team_pickup','yes')->update(['is_team_pickup'=>"no"]);
      //UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->where('is_forfet_knokout','yes')->update(['is_forfet_knokout'=>"no"]);
      return true;
    }
    public function updatePreviousRound ($l_id, $u_id, $r_id) {
    	
	      League::where('id',$l_id)->update(['current_round_id' => $r_id]);
		  League::where('id',$l_id)->where('process_status','complete')->update(['process_status' => 'progress']);
         
		
		   UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->where('status','active')->where('is_knockedout','yes')->update(['is_knockedout'=> "no"]);

		    UsersLeague::where('league_id',$l_id)->where('user_id',$u_id)->where('is_team_pickup','no')->where('is_forfet_knokout','!=','yes')->update(['is_team_pickup'=>"yes"]);

		    Round::where('id',$r_id)->update(['is_undu_able'=> "no",'process_status'=>"progress"]);
		    SavedTeam::where('user_id',$u_id)->where('league_id',$l_id)->where('round_id',$r_id)->update(['status' => null]);

	    return true;
	  }
    public function updatePointsLMS( $data ) {
       if($data['status']=='complete') {
           if($data['winnerId']==$data['userTeam']) {
              if($this->updateknockedOut($data['league_id'], $data['user_id'])) {
              	$this->updateRoundStats($data['league_id'], $data['user_id'], 'w', $data['round_id']);
				$slug = 'win_lms';
				$this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
				$this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
              	return true;
              } else {
              	return false;
              }
           } else {
	          UsersLeague::where('league_id',$data['league_id'])->where('user_id',$data['user_id'])->where('status','active')->update(['is_knockedout'=> "yes"]); 
	            $this->updateRoundStats($data['league_id'], $data['user_id'], 'l', $data['round_id']);
	            $slug = 'loss_lms';
				$this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
				$this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
           }
       } else {
       	    if($data['status']=='draw'){
       	    	 UsersLeague::where('league_id',$data['league_id'])->where('user_id',$data['user_id'])->where('status','active')->update(['is_knockedout'=> "yes"]); 
       	    	$this->updateRoundStats($data['league_id'], $data['user_id'], 'd', $data['round_id']);
       	    	$slug = 'loss_lms';
				$this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
				$this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
       	    }

       	    if($data['status']=='abandon') {
       	      if($this->updateknockedOut($data['league_id'], $data['user_id'])) {
	              	$this->updateRoundStats($data['league_id'], $data['user_id'], 'w',$data['round_id']);
					$slug = 'win_lms';
					$this->triggerNotification($slug, $data['user_id'], $data['fixture_id'],$data['league_id']);
					$this->mailNotification($slug, $data['user_id'], $data['league_id'], $data['round_id']);
	              	return true;
	              } else {
	              	return false;
	            }
       	    }
                 
       }
    }

    public function updateRoundStats ($l, $u, $status, $r) {

       $lastStatus = UsersLeague::where('league_id',$l)->where('user_id', $u)->where('status','active')->value('result_stats');
       $update= ''; 
       if(is_null($lastStatus)) {
        $update = $status;
       } else {
       	 $update = $lastStatus.','.$status;
       }
       SavedTeam::where('user_id',$u)->where('league_id',$l)->where('round_id',$r)->update(['status' => $status]);
       UsersLeague::where('league_id',$l)->where('user_id', $u)->where('status','active')->update(['result_stats' => $update]);
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
	          	League::where('id',$l_id)->update(['current_round_id' => $nextValue]);
	          	return true;
	          } else {
	          	League::where('id',$l_id)->update(['process_status' => 'complete']);
	          	return true;
	          }
	      } else {
	      	return false;
	      } 
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
    public function markWinner (array $data, $round_id, $sportID) {
    	
    	foreach ($data as $key => $value) {
    		foreach ($value['user_id'] as $keys => $val) {
    			$usrTeam = $this->model->where('league_id',$value['league_id'])->where('round_id',$round_id)->where('user_id',$val)->first();
    			UsersLeague::where('league_id',$value['league_id'])->where('user_id',$val)->where('status','active')->update(['is_forfet_knokout' => "no"]);
    			if( $usrTeam ) {
    				$fixture = Fixture::find($usrTeam->fixture_id)->toArray();
    				$asset = array(
	                  'userTeam' => $usrTeam->team_id ,
	                  'userFixture' => $usrTeam->fixture_id,
	                  'league_id' => $value['league_id'],
	                  'round_id' => $round_id,
	                  'user_id' => $val,
	                  'status' => $fixture['fixure_result_status'],
	                  'winnerId' => $fixture['winner_team_id'],
	                  'fixture_id' => $fixture['id']
	    			); 
	    			

    				if($value['league_type']=='lms') {
                      $this->updatePointsLMS($asset);
    				} else {
                       $this->updatePointsLML($asset);
    				}
    			} else {
    				$if_forfeit = League::where('id',$value['league_id'])->where('status','active')->value('if_forfeit');
    				UsersLeague::where('league_id',$value['league_id'])->where('user_id',$val)->where('status','active')->update(['is_forfet_knokout' => "no"]);
    				if($if_forfeit=='knocked_out') {
                      if($value['league_type']=='lms') { 
                      	if($this->updateknockedOut($value['league_id'], $val)) {
                         UsersLeague::where('league_id',$value['league_id'])->where('user_id',$val)->where('status','active')->update(['is_knockedout'=> "yes",'is_forfet_knokout' => "yes"]);
						 $this->updateRoundStats($value['league_id'], $val, 'l', $round_id);
						 $slug = 'loss_lms';
						 $this->triggerNotification($slug, $val, 1,$value['league_id']);
					    }
                      } else {
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
						$new->league_id = $value['league_id'];
						$new->round_id = $round_id;
						$new->fixture_id = 1;
						$new->final_goal_difference = $this->finalGD(0, $value['league_id'], $val);
						$new->save();
						$this->updateRoundStats($value['league_id'], $val, 'l', $round_id);
						$slug = 'loss_lml';
						$this->triggerNotification($slug, $val, 1,$value['league_id']);
						UsersLeague::where('league_id',$value['league_id'])->where('user_id',$val)->where('status','active')->update(['is_forfet_knokout' => "yes"]);
                      }

    				} else {
    					$lowestTeam = $this->getTeamforAutoSelection($sportID, $value['league_id'], $round_id, $val);
    					$fixt = $this->findFixtureByTeamId($value['league_id'], $lowestTeam);
    					$fxtDetail = Fixture::find($fixt)->toArray();
    					$insert = array(
                               'user_id' => $val,
                               'league_id' => $value['league_id'],
                               'round_id' => $round_id,
                               'team_id' => $lowestTeam,
                               'fixture_id' => $fixt
    					);
    					$this->model->create($insert);
    					$asset = array(
		                  'userTeam' => $lowestTeam ,
		                  'userFixture' => $fixt,
		                  'league_id' => $value['league_id'],
		                  'round_id' => $round_id,
		                  'user_id' => $val,
		                  'status' => $fxtDetail['fixure_result_status'],
	                      'winnerId' => $fxtDetail['winner_team_id'],
	                      'fixture_id' => $fxtDetail['id']
		    			); 

	    				if($value['league_type']=='lms') {
	                      $this->updatePointsLMS($asset);
	    				} else {
	                       $this->updatePointsLML($asset);
	    				}
	                     
    				}
    			}
    		}
    		$this->updateLeagueRound($value['league_id'], $round_id);
    	}
    	return true;
    }
    public function updateUnduInRoundTable($r_id, $s_id) {
      Round::where('sport_id',$s_id)->where('process_status','complete')->update(['is_undu_able'=> "no"]);
      Round::where('id',$r_id)->where('process_status','complete')->update(['is_undu_able'=> "yes"]);
    }
    public function getPreRoundStats(array $user) {
    	$lastArray = array();
    	foreach ($user as $key => $value) {
           	foreach ($value['user_id'] as $ky => $val) {
           		$lastResult = $this->model->where('league_id',$value['league_id'])->where('user_id', $val)->where('status','active')->value('result_stats');
           		$lastArray[$value['league_id']][$val] = $lastResult;
           	}
         }
         return json_encode($lastArray);
    }
    public function addUnduRecord (array $leagueUser, array $fixture,  $round_id, $sportID, $str) {

    	$userJson = json_encode( $leagueUser );
    	$fixture = json_encode( $fixture );

    	$insertionArray = array(
               'user_stats' => $userJson,
               'fixture_stats' => $fixture,
               'round_id' => $round_id,
               'last_result_stats' => $str
    	  );
    	$this->model->create($insertionArray);
    	Round::where('id',$round_id)->update(['process_status'=> "complete"]);
    	$this->updateUnduInRoundTable($round_id, $sportID);
    	return true;
    }
    public function getUndoRecord ( $id ) {
    	return $this->model->where('round_id',$id)->where('active','no')->value('id');
    }

    public function undoResultUpdate ( $id ) {
         Round::where('id',$id)->where('is_undu_able','yes')->update(['is_undu_able' => "running"]);
         return true;
    	// Working Code //
    	$data = $this->model->find( $id )->toArray();
    	if (!empty( $data )) {
           $userStats = json_decode($data['user_stats'],true);
           $lastResult = json_decode($data['last_result_stats'],true);
           foreach ($userStats as $key => $value) {
           	foreach ($value['user_id'] as $ky => $val) {
           		$this->removeUserStats($value['league_id'], $data['round_id'], $val);
             	$this->updatePreviousRound($value['league_id'], $val, $data['round_id']);
             	$update = $lastResult[$value['league_id']][$val];
             	$this->updateLastResultStats($value['league_id'], $val,$update);
           	}
           }
           $this->updateFixtureUndoStats($data['round_id']);
           
           /*
           $fixtures = json_decode($data['fixture_stats'],true);
           foreach ($fixtures as $ke => $val) {	
           	 $this->updateFixtureUndoStats($val['fixture_id']);
           }
           */
           $this->model->where('id',$id)->update(['active' => "yes"]);
           return true;
    	}
    }
    public function updateLastResultStats($lid, $uid, $update) {
    	 return UsersLeague::where('league_id',$lid)->where('user_id', $uid)->where('status','active')->update(['result_stats' => $update]);
    }
    public function removeUserStats ($lid, $rid, $uid) {
    	return userStats::where('league_id',$lid)->where('round_id',$rid)->where('user_id',$uid)->delete();
    }
    public function updateFixtureUndoStats ( $id ) {
      return Fixture::where('round_id',$id)->update(['fixure_result_status' => "pending"]);	
    }
    public function markAbondon($id) {
    	return Fixture::where('id',$id)->update(['fixure_result_status' => 'abandon']);
    }
    public function abondonHandler($fixture, $leagueUser, $round) {
      $temp = array();
      foreach ($fixture as $key => $value) {
      	if($value) {
      		$ids= explode('_', $key);
      		$temp[] = $ids;
      		$this->markAbondon($ids[1]);
      	}
      }
      $count = Fixture::where('round_id',$round)->where('status','active')->count('id');
      if($count==count($temp)) {
       return 'true';
      } else {
       return 'false';
      }

    }

}
