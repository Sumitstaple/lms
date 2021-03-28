<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;
use Modules\Frontend\Models\League;
use Modules\Frontend\Models\LeagueLocation;
use Modules\Frontend\Models\Round;
use Modules\Frontend\Models\Team;
use Modules\Sport\Models\Fixture;
use Modules\Result\Models\userStats;
use Modules\Frontend\Models\SavedTeam;
use Modules\Frontend\Models\UsersLeague;
use Auth;

class TeamSelection implements TeamSelectionInterface
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

    /**
     * Get's pagination.
     *
     * @return mixed
     */
    public function paginateWith($limit,$pageNumber,$relations=array())
    {
        return $this->model->with($relations)->paginate($limit, ['*'], 'page', $pageNumber);
    }
    public function paginateWithArray($limit,$pageNumber,$relations)
    {
        print_r($limit,$pageNumber,$relations); die;
        return $this->model->with($relations)->paginate($limit, ['*'], 'page', $pageNumber);
    }

    public function getApponent($f_id, $t_id) {
     $fixture = Fixture::find($f_id);
     $return  = 0;
     if(!empty($fixture)) {
       if($t_id == $fixture->home_team_id) {
         $return = $fixture->away_team_id; 
       } else {
         $return = $fixture->home_team_id;
       }
     }
     return $return;
    }
    public function lastWeekCalculation($l_id, $sport, $r_id, $team) {
		$return = 'false';
		
		$startTime = date("Y-m-d H:i:s",strtotime("-14 days"));
		$now = date('Y-m-d H:i:s');
        $getallRounds = Round::where('sport_id',$sport)->where('id', '<', $r_id)->whereBetween('start_datetime',[$startTime,$now])->pluck('id')->toArray();
	    if(!empty($getallRounds)) {
	      foreach ($getallRounds as $key => $value) {
	        $tsrt = SavedTeam::where('user_id',Auth::id())->where('league_id',$l_id)->where('round_id',$value)->first();
            if(!empty($team)) {
            	$apponent = $this->getApponent($tsrt->fixture_id, $tsrt->team_id);
            	
            }
	      }	
	    }
	    return $return;
    }

    public function getTeamDetail($id) {
    	$team = Team::find($id)->toArray();
    	return $team;
    }

   public function opponentHandler($r_id, $s_id, $l_id) {
      $round = Round::where('sport_id',$s_id)->orderBy('id')->pluck('id')->toArray();
      //$rountCounter = count( $round );
      $search = array_search($r_id, $round);
      $return = 'false';
      if(!empty($search)) {
          $newKey =  $search - 1;
          if(array_key_exists($newKey,$round)) {
          	$roundToId = $round[$newKey];
          	$tsrt = SavedTeam::where('user_id',Auth::id())->where('league_id',$l_id)->where('round_id',$roundToId)->first();
          	if(!empty($tsrt)) {
          		$apponent = $this->getApponent($tsrt->fixture_id, $tsrt->team_id);
          		$return = $apponent;
          	}
          }
      } 
      return $return;
   }
    public function getRoundNumberById( $u_id, $l_id, $t_id ) {
    	$round = SavedTeam::where('user_id',$u_id)->where('league_id',$l_id)->where('team_id',$t_id)->value('round_id');
    	return Round::find($round)->round_number;
    } 
    public function getTeamtoSelect($r_id, $s_id, $l_id) {
    
    	       $round = $this->model->find($r_id);
    	       $getFixture = Fixture::where('sport_id',$s_id)->where('round_id',$r_id)->where('status','active')->get()->toArray();
          	   $sportId = League::where('id',$l_id)->value('sport_id'); 
          	   $allTeam = Team::where('sport_id',$sportId)->where('is_active','yes')->pluck('id')->toArray();
          	   
               $myTeam = SavedTeam::where('user_id',Auth::id())->where('league_id',$l_id)->where('round_id','!=',$r_id)->pluck('team_id')->toArray();

               $allRound = Round::where('sport_id',$s_id)->orderBy('id')->pluck('id')->toArray();
               $rountCounter = count( $allRound );
               $allTeamCounter = count($allTeam);
               $roundNumber = array_search($r_id, $allRound) + 1;

                

              $sameLeagueMessage = '';
              $opponentMessage = '';
              $authId = Auth::id();
              //$filterFixture = array();
          	  foreach ($getFixture as $key => $value) {
          	  	$matchDate = date('D j M',strtotime($value['match_datetime']));
          	  	$matchTime = date('g.ia',strtotime($value['match_datetime']));
                $homeTeam = $this->getTeamDetail($value['home_team_id']);
				$awayTeam = $this->getTeamDetail($value['away_team_id']);
				$opponent = $this->opponentHandler($r_id, $s_id, $l_id);

				$home = 'true';
                $homeMesage = '';
                $away = 'true';
                $awayMesage = '';
                
          	    
          	    if($roundNumber < $allTeamCounter) {
          	    	if(in_array($value['home_team_id'], $myTeam)) {
                        $home = 'false';
                        $homeMesage = 'Picked in Round '.$this->getRoundNumberById($authId, $l_id, $value['home_team_id']);
          	    	} else {
                       if($opponent!='false') {
                       	  if($value['away_team_id'] == $opponent) {
                       	  	$home = 'false';
                            $homeMesage = 'Playing the opposition from the last round';
                       	  }
                       }
          	    	}

          	    	if(in_array($value['away_team_id'], $myTeam)) { 
                        $away = 'false';
                        $awayMesage = 'Picked in Round '.$this->getRoundNumberById($authId, $l_id, $value['away_team_id']);
          	    	} else {
                       if($opponent!='false') {
                       	  if($value['home_team_id'] == $opponent) {
                       	  	$away = 'false';
                            $awayMesage = 'Playing the opposition from the last round';
                       	  }
                       }
          	    	}
          	    } else {
          	    	if( $roundNumber % $allTeamCounter == 0 ){ 
	                    if(in_array($value['home_team_id'], $myTeam)) { 
	                        $home = 'false';
	                        $homeMesage = 'Picked in Round '.$this->getRoundNumberById($authId, $l_id, $value['home_team_id']);
	          	    	}
	          	    	if(in_array($value['away_team_id'], $myTeam)) { 
	                        $away = 'false';
	                        $awayMesage = 'Picked in Round '.$this->getRoundNumberById($authId, $l_id, $value['away_team_id']);
	          	    	}  
          	    	}
          	    	if( $roundNumber % $allTeamCounter == 1 ){ 
                       $home = 'true';
	                   $homeMesage = '';
	                   $away = 'true';
	                   $awayMesage = '';
          	    	}

          	    	if( $roundNumber % $allTeamCounter > 1 ){ 
                       $temp = abs($roundNumber - $roundNumber % $allTeamCounter);

                       $num = range($temp, $roundNumber-2);
                       
                       

                       $selectedRound = array();
                       foreach ($num as $key => $val) {
                       	 $selectedRound[] = $allRound[$val];
                       }

                    
                        
                       $myT = SavedTeam::where('user_id',Auth::id())->where('league_id',$l_id)->whereIn('round_id',$selectedRound)->pluck('team_id')->toArray();

                         


                       $opponentNumber = $allRound[$roundNumber - 2]; 

                       
                        $tsrt = SavedTeam::where('user_id',Auth::id())->where('league_id',$l_id)->where('round_id',$opponentNumber)->first();
                        
			          	if(!empty($tsrt)) {
			          		$apponent = $this->getApponent($tsrt->fixture_id, $tsrt->team_id);
			          	}
                           

                       if(in_array($value['home_team_id'], $myT)) { 
                          $home = 'false';
                          $homeMesage = 'Picked in Round '.$this->getRoundNumberById($authId, $l_id, $value['home_team_id']);
	          	    	} else {
	                       if($opponent!='false') {
	                       	  if($value['away_team_id'] == $apponent) {
	                       	  	$home = 'false';
	                            $homeMesage = 'Playing the opposition from the last round';
	                       	  }
	                       }
	          	    	}


	          	    	if(in_array($value['away_team_id'], $myT)) { 
	                        $away = 'false';
	                        $awayMesage = 'Picked in Round '.$this->getRoundNumberById($authId, $l_id, $value['away_team_id']);
	          	    	} else {
	                       if($opponent!='false') {
	                       	  if($value['home_team_id'] == $apponent) {
	                       	  	$away = 'false';
	                            $awayMesage = 'Playing the opposition from the last round';
	                       	  }
	                       }
	          	    	}

          	    	} 

          	    } 

                
          	  	$filterFixture[] = array(
						'fixture_id' => $value['id'],
						'home_team_id' => $value['home_team_id'],
						'home_team_name' => $homeTeam['team_name'],
						'home_team_icon' => $homeTeam['team_icon'],
						'away_team_id' => $value['away_team_id'],
						'away_team_name' => $awayTeam['team_name'],
						'away_team_icon' => $awayTeam['team_icon'],
						'home_is_selectable' => $home,
                        'away_is_selectable' => $away,
                        'matchDate' => $matchDate,
                        'matchTime' => $matchTime,
						'homemessage' => $homeMesage,
						'awaymesage' => $awayMesage
                    );
          	  }
          	  return $filterFixture;
    }

}
?>
