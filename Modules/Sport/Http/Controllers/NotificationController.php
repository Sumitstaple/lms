<?php

namespace Modules\Sport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \App\Laravue\JsonResponse;
use Image;
use Modules\Sport\Models\Notification;
use Auth;
use File;
use App\Repositories\SportRepository;
use App\Repositories\NotificationRepository;
use DB;

class NotificationController extends Controller
{
    protected $model;
 
    public function __construct(Notification $notification)
   {
       // set the model
       $this->model = new SportRepository($notification);
       $this->notification = new NotificationRepository($notification);
   }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {

        $rowsNumber = 1;   
        $data = [];
        $notifications = $this->notification->getOrderBy();

        $unreadCount = $this->notification->unReadCount();

        $uploadedColumn = array(
            'lms_sports' => 'sport',
            'users' => 'profile',
        );

        $notify = array();
        if(!$notifications->isEmpty()) {
        $image = "";
        foreach ($notifications as $key => $value) {

            // x.charAt(0)
            if(isset($uploadedColumn[$value->target_tbl])){
            $image = $uploadedColumn[$value->target_tbl].'/'.DB::table($value->target_tbl)->where('id',$value->target_id)->value($value->target_column);
          }
        // $image = $uploadedColumn[$value->target_tbl].'/'.DB::table($value->target_tbl)->where('id',$value->target_id)->value($value->target_column);    
        $is_image = 1;  
        $name = ''; 
        if($value->target_tbl == 'users'){
            $img = DB::table($value->target_tbl)->where('id',$value->target_id)->value($value->target_column);
            if($img == 'user.png') {
                $is_image = 0; 
                $full_name = '';
                $user = DB::table($value->target_tbl)->where('id',$value->target_id)->first();

                $full_name = $user->first_name;
                if($user->nickname != '') {
                    $full_name = $user->nickname;
                }
                $name = substr($full_name, 0,1);


            }

         }   

        $image = "";
          if(isset($uploadedColumn[$value->target_tbl])){
            $image = $uploadedColumn[$value->target_tbl].'/'.DB::table($value->target_tbl)->where('id',$value->target_id)->value($value->target_column);
          }


         $notify[] = array(
            'id' => $value->id,
            'message' => $value->message,
            'is_read' => $value->is_read,
            'date' => $value->created_at,
            'format' => $value->created_at->diffForHumans(),
            'image' => $image,

            'name' => $name,

            'league_id' => $value->league_id
         );
        }
      }

       //die("here");

        return response()->json(new JsonResponse(['items' => $notify, 'total' => $unreadCount]));
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
    public function getUnread(){
        $user_id = Auth::user()->id;
        $data = array('is_read'=>'no','user_id'=>$user_id);
        $notifications = $this->notification->where($data);
        return response()->json(new JsonResponse(['items' => $notifications, 'total' => count($notifications)]));
    }
    public function markasread(){

        $user_id = Auth::id();
        $data = $this->notification->update_mark_as_readAll($user_id,array('is_read' => 'yes'));

         $response = array(
            'status' => 'success',
        );
        return response()->json(new JsonResponse());
    }


}
