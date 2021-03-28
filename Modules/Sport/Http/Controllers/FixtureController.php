<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use Modules\Sport\Models\Fixture;
use Auth;
use File;
use Carbon\Carbon;
use App\Repositories\SportRepository;
use App\Permissions\HasPermissionsTrait;
use App\Http\Resources\UserResource;
use Modules\Sport\Models\Sport;
use App\Helper\SystemLogs;



class FixtureController extends Controller 
{

    protected $model; 
    use HasPermissionsTrait;

    public function __construct(Fixture $fixture, Sport $sport)
   {
       // set the model
       
        $this->userpermission = $this->haspermission(array('admin_permission'));
        
        if($this->userpermission == false){
            return response()->json(['error' => 'Unauthorized']);
            
        }

       $this->model = new SportRepository($fixture);
       $this->sport = new SportRepository($sport);
   }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        
        $rowsNumber = 1;    
        $data = [];

        if($request->string!='') {
        
         $fixture = $this->model->FixturepaginateWithSearch($request->limit, $request->page,array('sport','hometeam','awayteam','winnerteam','looserteam'),$request->string);  
        } else {

        $fixture = $this->model->paginateWith($request->limit, $request->page,array('sport','hometeam','awayteam','winnerteam','looserteam'));
         }
         $sport = $this->sport->getUniquSport();
        return response()->json(new JsonResponse(['items' => $fixture,'sport'=>$sport,'total' => 2]));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('fixture::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        
        $allData = $request->all();
        // print_r($allData);  die;
        for ($i=1; $i <= $request->fixture_number; $i++) {
            // echo $request->home_team_id[$i]; die;
                $data = array();
                $data['sport_id'] = $request->sport_id;
                $data['round_id'] = $request->round_id;
                $data['fixture_name'] = $request->fixture_name[$i];
                $data['home_team_id'] = $request->home_team_id[$i];
                $data['away_team_id'] = $request->away_team_id[$i];
                // $data['match_datetime'] = date('Y-m-d H:i:s',strtotime('+5 hour +30 minutes',strtotime($request->match_datetime[$i])));

                $data['match_datetime'] = $request->match_datetime[$i];

                $data['added_by'] = Auth::user()->id;

                // print_r($data); exit();

                $this->model->create($data);    
        }
        $response = array(
            'status' => 'success',
        );

        $sport_data = sport::where('id',$request->sport_id)->first();
        //System Logs
        $log_action = "Create Fixtures";
        $log_type = 3;
        $description = Auth::user()->first_name." ".Auth::user()->last_name." add ".$request->fixture_number." new fixtures under Sport ".$sport_data->sport_name." (ID::".$sport_data->id.")";
        SystemLogs::GenerateLogs($log_action,$description,$log_type);

        return response()->json(new JsonResponse(['total' => 2]));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */

    public function show($id)
    {
        $fixture = $this->model->withGet(array('sport','hometeam','awayteam','round'),$id);
        return response()->json(new JsonResponse($fixture));
    }
 
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('fixture::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        // $fixture_detail = Fixture::where('id',$id)->first();

        // if($fixture_detail->match_datetime == $request->match_datetime){
        //     $data['match_datetime'] = $fixture_detail->match_datetime;
        // }
        // else{
        //     $data['match_datetime'] = date('Y-m-d H:i:s',strtotime('+5 hour +30 minutes',strtotime($request->match_datetime)));
        // }
        $fixture = Fixture::where('id',$id)->first();
        $data['match_datetime'] = $request->match_datetime;
        $this->model->update($id,$data);
        $response = array(
            'status' => 'success',
        );

        $sport_data = sport::where('id',$request->sport_id)->first();
        //System Logs
        $log_action = "Edit Fixture";
        $log_type = 3;
        $description = Auth::user()->first_name." ".Auth::user()->last_name." edit a fixture ".$fixture->fixture_name." under Sport ".$sport_data->sport_name." (ID::".$sport_data->id.")";
        SystemLogs::GenerateLogs($log_action,$description,$log_type);

        return response()->json(new JsonResponse(['items' => $data]));
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

    public function updateFixtureStatus($id){

        $data = $this->model->update($id,array('status' => "de-active"));

        $response = array(
            'status' => 'success',
        );
        return response()->json(new JsonResponse(['data'=>$data]));
    }
    public function fixturebysportandround(Request $request,$sport_id,$round_id){
        $rowsNumber = 1;    
        $data = [];

        $fixture = $this->model->paginateWithbySportRound($request->limit, $request->page,array('sport','hometeam','awayteam','winnerteam','looserteam'),$sport_id,$round_id);
        $sport = $this->sport->getUniquSport();
        return response()->json(new JsonResponse(['items' => $fixture,'sport'=>$sport,'total' => 2]));
    }

}
