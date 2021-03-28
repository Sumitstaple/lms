<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use Modules\Sport\Models\League;
use Modules\Sport\Models\LeagueLocation;
use Modules\Sport\Models\Team;
use Modules\Sport\Models\Sport;
use Modules\Sport\Models\ManageTeams;
use Auth;
use File;
use App\Repositories\LeagueRepository;
use Modules\Frontend\Models\UsersLeague;
use Carbon\Carbon;
use Mail;  

class LeagueController extends Controller
{
    protected $model;  

    public function __construct(league $League, LeagueLocation $LeagueLocation)
   {
       // set the model
       $this->model = new LeagueRepository($League);
       $this->location = new LeagueRepository($LeagueLocation);
   } 
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {

        $rowsNumber = 1;   
        $data = [];
        $Leagues = $this->model->paginateWithLeagues($request->limit, $request->page,array('sport','round'));
        return response()->json(new JsonResponse(['items' => $Leagues, 'total' => 2]));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('league::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $allData = array();     
        
        // print_r($request->all());

        if($request->is_banterboard == "" ){
          $banter = 'no';
        }
        else{
            $banter = $request->is_banterboard;
        }

        $league = new League();
        $league->user_id = Auth::user()->id;
        $league->start_datetime = date("Y-m-d h:i:s", Carbon::now()->timestamp);
        $league->end_datetime = date("Y-m-d h:i:s", Carbon::now()->timestamp);
        $league->type = $request->league_type;
        $league->is_banterboard = $banter;
        $league->league_name = $request->league_name;
        $league->round_to_start = $request->round_to_start;
        $league->current_round_id = $request->round_to_start;
        $league->sport_id = $request->sport_id;
        $league->is_private = $request->is_private;
        $league->if_forfeit = $request->if_forfeit;

        if($league->save()){

          //save teams
        $checkteams = ManageTeams::where('sport_id',$request->sport_id)->get();

        if(count($checkteams) == 0){
            $getteams = Team::where('sport_id',$request->sport_id)->get();
            
            foreach ($getteams as $key => $value) {

              $manageteams = new ManageTeams();

              $manageteams->sport_id = $request->sport_id;
              $manageteams->team_id = $value->id;
              $manageteams->rank_order = $key+1;
              $manageteams->save();
            }
        }

            // print_r($request->all()); die;
            $locationData = new LeagueLocation();
            $locationData->league_id = $league->id;
            $locationData->league_town = $request->league_town;
            $locationData->league_city = $request->league_city;
            $locationData->league_state = $request->league_state;
            $locationData->league_country = $request->league_country;
            // print_r($locationData); die;
            if($locationData->save()){

                $leagueUsers = new UsersLeague();
                $leagueUsers->league_id=$league->id;
                $leagueUsers->user_id = Auth::user()->id;
                $leagueUsers->sport_id = $request->sport_id;
                $leagueUsers->is_admin = 'yes';
                $leagueUsers->is_team_pickup = 'no';
                $leagueUsers->is_knockedout = 'no';
                $leagueUsers->is_play = 'no';
                $leagueUsers->status='active';
                $leagueUsers->save();
                $response = array(
                    'status' => 'success',
                );

                $this->LeagueCreationEmailNotification($request->league_name);
                return response()->json(new JsonResponse($league));

                

            }
            else{
                $response = array(
                    'status' => 'fail',
                );
                return response()->json(new JsonResponse(array()));
            }
        }
        
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */

    public function show($id)
    {
        $league = $this->model->with(array('sport','round','location'))->where('id',$id)->get();
        return response()->json(new JsonResponse($league));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('league::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $Alldata = $request->all(); 
        // echo $request->is_banterboard;
        // print_r($Alldata); die;
        $data['league_name'] = $request->league_name;
        $data['sport_id'] = $request->sport_id;
        $data['round_to_start'] = $request->round_to_start;
        $data['current_round_id'] = $request->round_to_start;
        $data['type'] = $request->league_type;
        $data['is_private'] = $request->is_private;
        $data['if_forfeit'] = $request->if_forfeit;

        $data['is_banterboard'] = $request->is_banterboard;

        $data['start_datetime'] = date("Y-m-d h:i:s", Carbon::now()->timestamp);
        $data['end_datetime'] = date("Y-m-d h:i:s", Carbon::now()->timestamp);

        // print_r($data); exit();

        $abc = $this->model->update($id,$data);

        if($this->model->update($id,$data)){

                $locationData['league_town'] = (isset($Alldata['league_town']) && $Alldata['league_town'] != "") ;
                $locationData['league_city'] = $Alldata['league_city'];
                $locationData['league_state'] = $Alldata['league_state'];
                $locationData['league_country'] = $Alldata['league_country'];

                $this->location->updatedata(array('league_id'=>$id),$locationData);

                $response = array(
                    'status' => 'success',
                );
                return response()->json(new JsonResponse(['items' => $data]));

        }
        else{
                $response = array(
                    'status' => 'fail',
                );
                return response()->json(new JsonResponse(['items' => $data]));
            }

    }

    /** 
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateLeagueStatus($id){

       $data = $this->model->update_status($id,array('status' => 'de-active'));
        
        $response = array(
            'status' => 'success',
        );
        return response()->json(new JsonResponse(['data'=>$data]));
    }

    public function myleagues(){

        $user_id = Auth::user()->id;
        $data = $this->model->GetActiveLeagues(array('user_id' => $user_id),array('sport','round'));

        $response = array(
            'status' => 'success',
        );
        return response()->json(new JsonResponse(['items'=>$data]));
    }

    public function LeagueCreationEmailNotification($leagueName){
               
            $data = array();
            $data['name'] = 'Hello '.Auth::user()->first_name.' '.Auth::user()->last_name;
            $data['content'] = 'Your League '.$leagueName.' has been created.';
            $data['to'] = Auth::user()->email;

            if(Auth::user()->is_notifications_option_active == "yes"){

              Mail::send('emails.leaguecreate', $data, function($message) use ($data) {
                 $message->to($data['to'], 'Leauge Created')->subject
                    ('Leauge Created');
              });
            }
    } 

    public function manageteamlist($sport_id){

      // $sport_id = League::where('id',$leagueid)->value('sport_id');

      $getteams = Team::where('sport_id',$sport_id)->get();

      $getsavedteams = ManageTeams::where('sport_id',$sport_id)->orderBy('rank_order', 'ASC')->pluck('team_id');

      if(count($getsavedteams) > 0){
       $getteams=array();
        foreach ($getsavedteams as $key => $value) {
          $getteams[]= Team::where('id',$value)->first();
        }
        
      }

      $sort = 1;
      foreach ($getteams as $key => $value) {
        $sport_name = Sport::where('id',$value->sport_id)->value('sport_name');
        $value['sportname'] = $sport_name;
        $value['sort'] = $sort;
        $sort++;
      }

      return response()->json(new JsonResponse(['items'=>$getteams]));

    }

    public function saveteamlist(Request $request){

      // print_r($request->all());

      $arr = $request->data;

      // print_r($arr);

      $data = array_combine(range(1, count($arr)), array_values($arr));

      $getPreLeagueTeam = ManageTeams::where('sport_id',$request->sportid)->delete();

      foreach ($data as $key => $value) {

        $res = json_decode($value);

        $manageteams = new ManageTeams();

        $manageteams->sport_id = $request->sportid;
        $manageteams->team_id = $res->id;
        $manageteams->rank_order = $key;
        $manageteams->save();
      }
    }

}
