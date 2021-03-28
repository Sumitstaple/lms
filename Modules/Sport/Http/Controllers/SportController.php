<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use Modules\Sport\Models\Sport;
use Modules\Sport\Models\Team;
use Auth;
use File;
use App\Repositories\SportRepository;
use Illuminate\Support\Facades\Validator;
use App\Helper\SystemLogs;

class SportController extends Controller
{
    protected $model; 

    public function __construct(Sport $sport)
   {
       // set the model
       $this->model = new SportRepository($sport);
   }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $rowsNumber = 1;   
        $data = [];
        $sports = $this->model->paginate($request->limit, $request->page);
        return response()->json(new JsonResponse(['items' => $sports]));
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
        
       
        $temp = $request->all();


        if(isset($temp['type']) && $temp['type']=='single') {

           $validator = Validator::make($request->all(), [
            'image' => 'required|base64image:jpeg,jpg,png'
            ]);

         if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
          }

            $path1 = public_path('uploads');
            if (!File::exists($path1)) {
              File::makeDirectory($path1, 0777, true);
            }

            $path2 = public_path('uploads/team');
            if (!File::exists($path2)) {
              File::makeDirectory($path2, 0777, true);
            }
            $sport_icon = "team-".time().".png";
            $path = public_path().'/uploads/team/' . $sport_icon;
            Image::make(file_get_contents($request['image']))->save($path);
            $temp['image']  = $sport_icon; 
            return response()->json(new JsonResponse(['status' => 'success','data' => $temp]));
        }
        

        $validator = Validator::make($request->all(), [
        'sport_name' => 'required',
        'sport_icon' => 'required|base64image:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {
            $data = $request->all();
            $path1 = public_path('uploads');
            if (!File::exists($path1)) {
              File::makeDirectory($path1, 0777, true);
            }

            $path2 = public_path('uploads/sport');
            if (!File::exists($path2)) {
              File::makeDirectory($path2, 0777, true);
            }
            $sport_icon = "icon-".time().".png";
            $path = public_path().'/uploads/sport/' . $sport_icon;
            Image::make(file_get_contents($request->sport_icon))->save($path);   

            $data['sport_icon'] = $sport_icon;
            $data['added_by'] = Auth::user()->id;
            $this->model->create($data);
            $last_id = Sport::orderBy('id', 'DESC')->first();

            $teamIcon = array_filter($request->team_icon);
            $teamName = array_filter($request->team_name);
           
            $adminId = Auth::user()->id;
            $sportId = $last_id->id;
            foreach ($teamName as $key => $value) {
                $saveTeam = array(
                  'added_by' => $adminId,
                  'team_icon' =>  $teamIcon[$key],
                  'team_name' => $value,
                  'sport_id' => $sportId
                );
                $this->saveTeamIcon($saveTeam);
            }


            //System Logs
            $log_action = "Create Sport";
            $log_type = 1;
            $description = Auth::user()->first_name." ".Auth::user()->last_name." add a new sport ".$request->sport_name." (ID::".$last_id->id.")";
            SystemLogs::GenerateLogs($log_action,$description,$log_type);
            return response()->json(new JsonResponse(['status' => 'success']));
        }
        
    }

    public function saveTeamIcon($data) {
        return Team::create($data);
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
        $valid['sport_name'] = 'required';

        if (preg_match('/^data:/', $request->team_icon)) {
            $valid['sport_icon'] = 'required|base64image:jpeg,jpg,png';
        } 

        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        } else {
            if (preg_match('/^data:image\/(\w+);base64,/', $request->sport_icon)) {
                $sport_icon = "icon-".time().".png";
                $path = public_path().'/uploads/sport/' . $sport_icon;
                Image::make(file_get_contents($request->sport_icon))->save($path);   
                $data['sport_icon'] = $sport_icon;
            }
            $data['added_by'] = Auth::user()->id;
            $this->model->update($id,$data);

            //System Logs
            $log_action = "Edit Sport";
            $log_type = 1;
            $description = Auth::user()->first_name." ".Auth::user()->last_name." edit a sport ".$request->sport_name." (ID::".$id.")";
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
    public function sportsforfixture(){

        $fixture = $this->model->all();
        return response()->json(new JsonResponse(['items' => $fixture, 'total' => 2]));
    }
}
