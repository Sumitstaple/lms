<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Frontend\Models\UsersLeague;
use Modules\Frontend\Models\League;
use Modules\Frontend\Models\Invite;
use Modules\Sport\Models\NotificationTemplate;
use Modules\Sport\Models\Notification;
use \App\Laravue\JsonResponse;
use App\Laravue\Models\User;
use App\Laravue\Models\PermissionGroup;
use App\Repositories\SportRepository;
use App\Repositories\NotificationRepository;
use Auth; 
use DB;
use App\Jobs\SendEmail; 
use App\Helper\NotificationHandler;
use Modules\Frontend\Models\SavedTeam; 
use Modules\Sport\Models\Team;
use Mail; 

class UsersLeagueController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

  public function __construct(UsersLeague $UsersLeague, NotificationTemplate $NotificationTemplate,Notification $Notification,League $league )
   {
       // set the model
       $this->model = new SportRepository($UsersLeague);
       $this->leagueModel = new SportRepository($league);
       $this->template = new NotificationRepository($NotificationTemplate);
       $this->notification = new NotificationRepository($Notification);
   }

    public function index()
    {
        $rowsNumber = 1; 
        $data = [];
        $user_id = Auth::user()->id;
        $post_data = array('user_id'=>$user_id,'status'=>'active');
        $Usersleague = $this->model->GetActiveLeagues($post_data,array('league','user','sport'));
        foreach ($Usersleague as $key => $value) {
            $team = SavedTeam::where('user_id',Auth::user()->id)->where('league_id',$value->league->id)->where('round_id',$value->league->current_round_id)->first();
            //print_r($value); die;
            if($team){
                $teamdata = Team::where('id',$team->team_id)->first();
                $value->league['team']=$teamdata;
            }
            else{
              $value->league['team'] = 'null';
            }
        }
        return response()->json(new JsonResponse(['items' => $Usersleague, 'total' => 2]));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('frontend::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }



    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('frontend::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function userleaguedetail($league_id){
        $post_data = array('league_id'=>$league_id);
        $leagueDetail = $this->model->GetActiveLeagues($post_data,array('league','user'));

        foreach ($leagueDetail as $key => $value) {
         
          $team = SavedTeam::where('user_id',$value->user->id)->where('league_id',$value->league->id)->where('round_id',$value->league->current_round_id)->first();

          if($team){
              $teamdata = Team::where('id',$team->team_id)->first();
              
              $value['teamname']=$teamdata->team_name;
          }  
          else{
            $value['teamname']='null';
          }  
        }

        return response()->json(new JsonResponse(['items' => $leagueDetail, 'total' => 2]));

    }

    public function currentuserleague($userid,$league_id){
        $post_data = array('league_id'=>$league_id,'user_id'=>$userid);
        $leagueDetail = $this->model->GetActiveLeagues($post_data,array('league','user'));
        return response()->json(new JsonResponse(['items' => $leagueDetail, 'total' => 2]));
    }

    public function makeadmin($league_id,$user_id,$status){

        //Permissions

        $Getpermission = PermissionGroup::where('id',3)->first();
        $Getpermission = explode(",",$Getpermission['permission_ids']);

       

        //Get User Permissions

        $userData = User::where('id',$user_id)->first();
        $getCurrentPrmission = explode(",",$userData['access_permissions']);

        if($status == 'yes'){

            $newPermissions = array_merge($Getpermission,$getCurrentPrmission);
            $newPermissions = array_unique($newPermissions);

            $updateuser = User::find($user_id);
            $updateuser->access_permissions= implode(',',$newPermissions);
            if($updateuser->save()){
                $data = $this->model->MakeLeagueAdmin($league_id,$user_id,array('is_admin'=>$status));

                $this->makeleagueAdminSystemNotification($league_id,$user_id,$status);

                return response()->json(new JsonResponse(['status' => 'leagueadmin']));
            }
        }
        else{

            $newPermissions = array_diff($getCurrentPrmission,$Getpermission);
            // $newPermissions = array_unique($newPermissions);
            $updateuser = User::find($user_id);
            $updateuser->is_mark_admin= 'no';
            $updateuser->access_permissions= implode(',',$newPermissions);
            if($updateuser->save()){
              $data = $this->model->MakeLeagueAdmin($league_id,$user_id,array('is_admin'=>$status));
              $this->makeleagueAdminSystemNotification($league_id,$user_id,$status);
              return response()->json(new JsonResponse(['status' => 'user']));
            }
        }


    }

    /**
     * Join leauge users.
     * @param int $id
     * @return Response
     */
    public function joinUser(Request $request) {

        $leagueId = base64_decode($request->lms);
        $inviteId = base64_decode($request->invite_id);

        $userId = Auth::user()->id;
        $postData = array('user_id'=>$userId,'league_id'=>$leagueId);
        $userLeauge = $this->model->multiWhere($postData)->first();


        $check_leauge = $this->leagueModel->withGet(array('user'),$leagueId);
        if($check_leauge) {
            $invite = Invite::where('id',$inviteId)->where('leagues_id',$leagueId)->first();
            if($invite || $request->mode == 'fb' || $request->mode == 'tw') {
                if(isset($invite) && $invite->email != Auth::user()->email) {

                     // return response()->json(new JsonResponse(['status' => 'emailWrong', 'league_name' => $check_leauge->league_name]));
                     
                    }
            } else {
                return response()->json(new JsonResponse(['status' => 'urlInvalid', 'league_name' => $check_leauge->league_name]));
            }
        } else {
                return response()->json(new JsonResponse(['status' => 'urlInvalid', 'league_name' => $check_leauge->league_name]));
        }

        if($userLeauge) {
            return response()->json(new JsonResponse(['status' => 'exist', 'league_name' => $check_leauge->league_name]));
        } else {
            $data['league_id'] = $leagueId;
            $data['user_id'] = $userId;
            $data['is_team_pickup'] = 'no';
            $data['is_admin'] = 'no';
            $data['status'] = 'active';
            $data['is_play'] = 'yes';
            $data['sport_id'] = $check_leauge->sport_id;
            $this->model->create($data);
            return response()->json(new JsonResponse(['status' => 'active', 'league_name' => $check_leauge->league_name]));
        }
    }


    /**
     * user search leauge then join.
     * @param int $id
     * @return Response
     */
    public function userJoinLeauge(Request $request) {

        $leagueId = $request->id;

        $post_data = array('league_id'=>$leagueId, 'user_id'=>Auth::user()->id);

        $userLeauge = $this->model->withWhereGet($post_data,array('user'));

        $check_leauge = $this->leagueModel->withGet(array('user'),$leagueId);

        if(!$userLeauge->isEmpty()) {

            if($check_leauge->is_private == "yes"){
                return response()->json(new JsonResponse(['status' => 'existprivate']));
            }    
            else{

                return response()->json(new JsonResponse(['status' => 'exist']));
            }
        } else {

                if($check_leauge->is_private == "yes"){
                  $data['league_id'] = $check_leauge->id;
                  $data['user_id'] = Auth::user()->id;
                  $data['is_team_pickup'] = 'no';
                  $data['is_admin'] = 'no';
                  $data['sport_id'] = $check_leauge->sport_id;
                  $data['status'] = 'request_pending';
                  $data['is_play'] = 'yes';

                  $this->privateLeagueSystemNotification($check_leauge->id);

                  $this->model->create($data);

                  return response()->json(new JsonResponse(['status' => $data['status'], 'league_name' => $check_leauge->league_name]));

                }

                else{

                $data['league_id'] = $leagueId;
                $data['user_id'] = Auth::user()->id;
                $data['is_team_pickup'] = 'no';
                $data['is_admin'] = 'no';
                $data['sport_id'] = $check_leauge->sport_id;
                $data['is_play'] = 'yes';
                if ($check_leauge->is_private == 'yes') {
                    $data['status'] = 'request_pending';
                } else {
                    $data['status'] = 'active';
                }
                    $this->model->create($data);
                    $this->triggerNotification( $leagueId );
                    return response()->json(new JsonResponse(['status' => $data['status'], 'league_name' => $check_leauge->league_name]));
                }
            }
    }

    /**
     * Accept leauge by leauge admin.
     * @param int $id,user_id
     * @return Response
     */
    public function acceptRequest($id, $user_id,$status) {

        // $data = array('status' => $status);

        $data = $this->model->update_status($id,array('status' => $status));
        $this->acceptRequestNotification($id,$user_id,$status);
        $this->AcceptRequestEmailNotification($id,$user_id,$status);
        return response()->json(new JsonResponse(['status' => 'success']));
    }
 
    /**
     * Join private leauge leauge users.
     * @param int $id
     * @return Response
     */

    public function acceptRequestNotification($id,$user_id,$status){

            if($status == "active"){
                $status = "Accepted";
            }
             $userleague = UsersLeague::where('id',$id)->value('league_id');
             $name = League::where('id', $userleague)->value('league_name');
             $constantArray = array('{{leaugeName}}','{{status}}');
             $uname=  ucfirst(Auth::user()->first_name);

             $valueArray = array($name, $status);
                $notify = array(
                        'slug' => 'leaugeJoinrequestaccept',
                        'userId' => $user_id,
                        'type' => 'leaugeJoin',
                        'targetTable' => 'users',
                        'targetCol' => 'avatar',
                        'targetId' => Auth::user()->id,
                        'leagueId' => $userleague,
                    ); 

            NotificationHandler::save($constantArray , $valueArray, $notify);
    }
    public function AcceptRequestEmailNotification($id,$user_id,$status){
            if($status == "active"){
                $status = "accepted";
            }
            $data = array();

            $userdata = User::where('id',$user_id)->first();

            $first_name = $userdata->first_name;
            $last_name = $userdata->last_name;
            $email = $userdata->email;
            $is_email_notification = $userdata->is_notifications_option_active;
            if($is_email_notification == "yes"){
              
              $userleague = UsersLeague::where('id',$id)->value('league_id');
              $name = League::where('id', $userleague)->value('league_name');

              $data['name'] = 'Hello '.$first_name.' '.$last_name;
              $data['content'] = 'Your request to join League '.$name.' has been '.$status.'.';
              $data['to'] = $email;

            
              Mail::send('emails.leaguecreate', $data, function($message) use ($data) {
                 $message->to($data['to'], 'Leauge Join Request')->subject
                    ('Leauge Join Request');
              });
            }
    }
    public function joinUserInPrivateLeauge($leagueId,$inviteId,$check_leauge) {
        if($check_leauge) {
            $data['league_id'] = $leagueId;
            $data['user_id'] = Auth::user()->id;
            $data['is_team_pickup'] = 'no';
            $data['is_admin'] = 'no';
            if ($check_leauge->is_private == 'yes') {
            $data['status'] = 'request_pending';
            /*Send email to league admin saying User XX has asked for permission to enter */
            // $mailData = array();
            // $mailData['name'] = $check_leauge->user->first_name.' '.$check_leauge->user->last_name;
            // $mailData['content'] = 'User '.Auth::user()->first_name.' '.Auth::user()->last_name.' has asked for permission to enter';
            // // $to = $check_leauge->user->email;
            // $to = 'gurpreet@staplelogic.in';
            // $subject = 'Leauge Join request';
            // SendEmail::dispatch($to, $subject, $mailData)->onQueue('emails');

            } else {
                $data['status'] = 'active';
            }
            $this->model->create($data);
            return response()->json(new JsonResponse(['status' => $data['status'], 'league_name' => $check_leauge->league_name]));
        }
    }

    public function getList($id)
    {
        $leagueUsers = $this->model->addWhere('league_id',$id)->with('user')->get();
        return response()->json(new JsonResponse(['items' => $leagueUsers]));
    }

    public function joinPrivateLeauge($check_leauge) {

          if($check_leauge) {
            // $data['league_id'] = $check_leauge->id;
            // $data['user_id'] = Auth::user()->id;
            // $data['is_team_pickup'] = 'no';
            // $data['is_admin'] = 'no';
            // $data['sport_id'] = $check_leauge->sport_id;
            // if ($check_leauge->is_private == 'yes') {
            // $data['status'] = 'request_pending';

            // $this->privateLeagueSystemNotification($check_leauge->id);

            // // $this->model->create($data);

            // return response()->json(new JsonResponse(['status' => $data['status'], 'league_name' => $check_leauge->league_name]));
            // die("here");
            // } 

        }
    }

    public function triggerNotification($leagueId) {
    	     $name = League::where('id', $leagueId)->value('league_name');
    	     $leagueAdmins = UsersLeague::where('league_id', $leagueId)->where('is_admin','yes')->pluck('user_id')->toArray();
    	     $constantArray = array('{{leaugeName}}','{{userName}}');
    	     $uname=  ucfirst(Auth::user()->first_name);
             $valueArray = array($name, $uname);
             if(!empty($leagueAdmins)) {
             	 foreach ($leagueAdmins as $key => $value) {
	             	$notify = array(
		                'slug' => 'leaugeJoin',
		                'userId' => $value,
		                'type' => 'leaugeJoin',
		                'targetTable' => 'users',
		                'targetCol' => 'avatar',
		                'targetId' => Auth::user()->id,
                    'leagueId' => $leagueId,
	                );
	                NotificationHandler::save($constantArray , $valueArray, $notify);
	            }
             }
             
    }

        public function makeleagueAdminSystemNotification($leagueId,$userid,$status) {

             $name = League::where('id', $leagueId)->value('league_name');
             
             $constantArray = array('{{message}}');
             $uname=  ucfirst(Auth::user()->first_name);

             if($status == 'yes'){
                $message = $uname.' added you as a an admin of '.$name;
             }
             else{
              $message = $uname.' remove you as a admin of the league '.$name;
             }

            $valueArray = array($message);
           
            $notify = array(
                'slug' => 'makeleagueadmin',
                'userId' => $userid,
                'type' => 'MakeLeagueAdmin',
                'targetTable' => 'users',
                'targetCol' => 'avatar',
                'targetId' => Auth::user()->id,
                'leagueId' => $leagueId,
            );

            NotificationHandler::save($constantArray , $valueArray, $notify);

           $this->privateLeagueEmailNotification($leagueId);
             
    }

    public function privateLeagueSystemNotification($leagueId) {

             $name = League::where('id', $leagueId)->value('league_name');
             $leagueAdmins = UsersLeague::where('league_id', $leagueId)->where('is_admin','yes')->pluck('user_id')->toArray();
             $constantArray = array('{{leaugeName}}','{{userName}}');
             $uname=  ucfirst(Auth::user()->first_name);

             $valueArray = array($name, $uname);
             // print_r($leagueAdmins); die;
             if(!empty($leagueAdmins)) {

                 foreach ($leagueAdmins as $key => $value) {

                    $notify = array(
                        'slug' => 'leaugeJoinrequest',
                        'userId' => $value,
                        'type' => 'leaugeJoin',
                        'targetTable' => 'users',
                        'targetCol' => 'avatar',
                        'targetId' => Auth::user()->id,
                        'leagueId' => $leagueId,
                    );

                    NotificationHandler::save($constantArray , $valueArray, $notify);

                   $this->privateLeagueEmailNotification($leagueId);
                }
                
             }
             
    }

    public function privateLeagueEmailNotification($leagueId){
            
            $check_leauge = $this->leagueModel->withGet(array('user'),$leagueId);
            /*Send email to league admin saying User XX has asked for permission to enter */

            if($check_leauge->user->is_notifications_option_active == "yes"){

              $data = array();
              $data['name'] = 'Hello,'. $check_leauge->user->first_name.' '.$check_leauge->user->last_name;
              $data['content'] =  'User '.Auth::user()->first_name.' '.Auth::user()->last_name.' has asked for permission to enter in <a href="'.url("/#/league/user-league-detail/").'/'.$leagueId.'">'.$check_leauge->league_name.'</a>';
              $data['to'] = $check_leauge->user->email;
              $subject = 'Leauge Join request';

              Mail::send('emails.leaguecreate', $data, function($message) use ($data) {
                 $message->to($data['to'], 'Leauge Join Request')->subject
                    ('Leauge Join Request');
              });
            } 

    }

}
