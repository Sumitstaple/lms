<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use Modules\Sport\Models\Round;
use Modules\Sport\Models\Sport;
use Auth;
use File;
use App\Repositories\SportRepository;
use App\Helper\SystemLogs;
 
class RoundController extends Controller
{
    protected $model;

    public function __construct(Round $round, Sport $sport)
   {
       // set the model
       $this->model = new SportRepository($round);
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
        
         $rounds = $this->model->paginateWithSearch($request->limit, $request->page,'sport',$request->string);  
        } else {

         $rounds = $this->model->paginateWith($request->limit, $request->page,'sport'); 
        }

        $sport = $this->sport->getUniquSport();
        return response()->json(new JsonResponse(['items' => $rounds,'sport'=>$sport]));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('sport::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $allData = $request->all();
        for ($i=1; $i <= $request->round_number; $i++) {
                $data = array();
                $data['sport_id'] = $request->sport_id;
                $data['round_number'] = $i;
                $data['round_name'] = $request->round_name[$i];
                $data['round_description'] = 'NULL';
                //$data['round_description'] = $request->round_description[$i];
                $data['added_by'] = Auth::user()->id;
                $data['start_datetime'] = $request->start_datetime[$i];
                $data['end_datetime'] = $request->end_datetime[$i];
                $this->model->create($data);
            
        }
        
        
        $response = array(
            'status' => 'success',
        );

        $sport_data = sport::where('id',$request->sport_id)->first();
        //System Logs
        $log_action = "Create Rounds";
        $log_type = 3;
        $description = Auth::user()->first_name." ".Auth::user()->last_name." add ".$request->round_number." new round under Sport ".$sport_data->sport_name." (ID::".$sport_data->id.")";
        SystemLogs::GenerateLogs($log_action,$description,$log_type);

        return response()->json(new JsonResponse(['items' => 1, 'total' => 2]));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $sports = $this->model->get($id);
        return response()->json(new JsonResponse($sports));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('sport::edit');
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
        

        $data['start_datetime'] = $request->start_datetime;
        $data['end_datetime'] = $request->end_datetime;

        $this->model->update($id,$data);
        $response = array(
            'status' => 'success',
        );

        $round_data = round::where('id',$id)->first();
        $sport_data = sport::where('id',$round_data->sport_id)->first();
        //System Logs
        $log_action = "Edit Round";
        $log_type = 3;
        $description = Auth::user()->first_name." ".Auth::user()->last_name." edit a round ".$round_data->round_name." under Sport ".$sport_data->sport_name." (ID::".$sport_data->id.")";
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

    /* Get Rounds according to specific sport */

    public function fetchroundsbysport($sport_id)
    {
        //die("here");
        $rounds = $this->model->wheredate($sport_id)->get();
        return response()->json(new JsonResponse($rounds));
    }

    public function fetchroundsbysportleague($sport_id)
    {
        //die("here");
        $rounds = $this->model->wheredateleague($sport_id)->get();
        return response()->json(new JsonResponse($rounds));
    }

    // Get sports without pagination
    public function roundsforfixture(){

        $fixture = $this->model->all();
        return response()->json(new JsonResponse(['items' => $fixture, 'total' => 2]));
    }

    public function sportsrounds(Request $request, $sport_id)
    {
        $data = [];
       
        $teams = $this->model->SportsRoundsWithPaginate($request->limit, $request->page,'sport',$sport_id);
       
        return response()->json(new JsonResponse(['items' => $teams]));
    }
}
