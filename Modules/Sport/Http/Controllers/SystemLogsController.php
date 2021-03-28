<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use App\SystemLog;
use App\User;
use Auth;
use File;
use App\Repositories\SportRepository;
use DB;

class SystemLogsController extends Controller
{
    protected $model;
 
   public function __construct(SystemLog $logs,User $user)
   {
       // set the model
       $this->model = new SportRepository($logs);
       $this->user = new SportRepository($user);
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
        
         $result = $this->model->logpaginateWithSearch($request->limit, $request->page,array('logtype','user'),$request->string);  
        } else {

         $result = $this->model->logpaginateWith($request->limit, $request->page,array('logtype','user')); 
        }

        $user = User::get();
        return response()->json(new JsonResponse(['items' => $result,'user'=>$user]));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('notification::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

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
        return view('notification::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

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

}
