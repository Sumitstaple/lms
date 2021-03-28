<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use Modules\Sport\Models\Team;
use Auth;
use File;
use App\Repositories\SportRepository;
use Illuminate\Support\Facades\Validator;
use App\Helper\SystemLogs;

use Modules\Sport\Models\Sport; 

class TeamController extends Controller
{ 
    protected $model;
 
    public function __construct(Team $team, Sport $sport)
   {
       // set the model
       $this->model = new SportRepository($team);
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
            $teams = $this->model->teampaginateWithSearch($request->limit, $request->page,'sport',$request->string);
        }
        else{

            $teams = $this->model->paginateWith($request->limit, $request->page,'sport');
        }
        $sport = $this->sport->getUniquSport();
        return response()->json(new JsonResponse(['items' => $teams,'sport'=>$sport]));
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
        $data = $request->all();
        $validator = Validator::make($request->all(), [
        'team_name' => 'required',
        'sport_id' => 'required',
        'team_icon' => 'required|base64image:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {
            $path1 = public_path('uploads');
            if (!File::exists($path1)) {
              File::makeDirectory($path1, 0777, true);
            }

            $path2 = public_path('uploads/team');
            if (!File::exists($path2)) {
              File::makeDirectory($path2, 0777, true);
            }
            $team_icon = "icon-".time().".png";
            $path = public_path().'/uploads/team/' . $team_icon;
            Image::make(file_get_contents($request->team_icon))->save($path);   

            $data['team_icon'] = $team_icon;
            $data['added_by'] = Auth::user()->id;
          
            $this->model->create($data);

            $last_id = Team::orderBy('id', 'DESC')->first();

            $sport_data = sport::where('id',$request->sport_id)->first();
            //System Logs
            $log_action = "Create Team";
            $log_type = 2;
            $description = Auth::user()->first_name." ".Auth::user()->last_name." add a new team ".$request->team_name." under Sport ".$sport_data->sport_name." (ID::".$sport_data->id.")";
            SystemLogs::GenerateLogs($log_action,$description,$log_type);

            return response()->json(new JsonResponse(['status' => 'success']));
        }    

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
        $valid = [];
        $valid['team_name'] = 'required';
        $valid['sport_id'] = 'required';

        $teamdata = Team::where('id',$id)->first();

        if (preg_match('/^data:/', $request->team_icon)) {
            $valid['team_icon'] = 'required|base64image:jpeg,jpg,png';
        } 

        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        } else {
            if (preg_match('/^data:image\/(\w+);base64,/', $request->team_icon)) {
                $team_icon = "icon-".time().".png";
                $path = public_path().'/uploads/team/' . $team_icon;
                Image::make(file_get_contents($request->team_icon))->save($path);   
                $data['team_icon'] = $team_icon;
            }
            $data['added_by'] = Auth::user()->id;
            $this->model->update($id,$data);
            $response = array(
                'status' => 'success',
            );

            $sport_data = sport::where('id',$teamdata->sport_id)->first();
            //System Logs
            $log_action = "Edit Team";
            $log_type = 2;
            $description = Auth::user()->first_name." ".Auth::user()->last_name." edit a team ".$teamdata->team_name." (ID::".$id.") under Sport ".$sport_data->sport_name." (ID::".$sport_data->id.")";
            SystemLogs::GenerateLogs($log_action,$description,$log_type);

            return response()->json(new JsonResponse(['status' => 'success']));
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

    // Get sports without pagination
    public function teamsforfixture($id){
        // die("here");
        $teams = $this->model->where($id)->get();

        return response()->json(new JsonResponse(['items' => $teams, 'total' => 2]));
    }

    public function sportsteams(Request $request, $sport_id)
    {
        $data = [];
       
        $teams = $this->model->SportsTeamsWithPaginate($request->limit, $request->page,'sport',$sport_id);
       
        return response()->json(new JsonResponse(['items' => $teams]));
    }
}
