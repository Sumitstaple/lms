<?php

namespace Modules\Frontend\Http\Controllers;

use App\Jobs\SendWelcomeEmail;
use Illuminate\Http\Request;
use Response;
use Illuminate\Routing\Controller;
use Modules\Frontend\Models\Invite;
use Mail;
use App\User;
use Auth;
use Modules\Frontend\Models\League;
use Modules\Frontend\Models\LeagueComment;
use Modules\Frontend\Models\LeagueCommentStatus;
use Modules\Frontend\Models\Round;
use Modules\Frontend\Models\Team;
use Modules\Sport\Models\Fixture;
use Modules\Frontend\Models\SavedTeam;
use App\Repositories\SportRepository;
use App\Laravue\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Helper\NotificationHandler;
use Modules\Frontend\Models\UsersLeague;


class LeagueCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(League $League, LeagueComment $LeagueComment,LeagueCommentStatus $LeagueCommentStatus,UsersLeague $UsersLeague )
   {
       $this->league = new SportRepository($League);
       $this->model = new SportRepository($LeagueComment);
       $this->commentCounter = new SportRepository($LeagueCommentStatus);
       $this->usersLeague = new SportRepository($UsersLeague);
   }

    /**
     * get League comments.
     * @param int $id
     * @return Response
     */
    public function index($leaugeId, $roundId) {
      $userId = Auth::user()->id;
      $postData = array('user_id'=>$userId,'league_id'=>$leaugeId);
      $userLeauge = $this->usersLeague->multiWhere($postData)->first();

      if(!$userLeauge) {
        return response()->json(new JsonResponse(['items' => array(),'status' => 'fail']));
      }

      $post_data = array('parent_comment_id' => 0, 'league_id' => $leaugeId, 'status' => 'active');
      $coments = $this->model->withWhereGet($post_data,array('user'));

      $comments = array();
      foreach ($coments as $key => $value) {
        $data = array();
        $subComments = array();
        $data['id'] = $value->id;
        $data['comment'] = $value->comment;
        $data['pre_comment'][$value->id] = $value->comment;
        $data['time'] = $this->timeAgo($value->created_at);
        $data['user'] = $value->user->nickname != ''? $value->user->nickname:$value->user->first_name.' '.$value->user->lst_name;
        $data['profile_pic'] = $value->user->avatar;
        $data['edit'] = false;
        $data['reply'] = false;
        $data['action'] = $value->user_id == Auth::user()->id?1:0;

        $sub_post_data = array('parent_comment_id' => $value->id, 'sub_parent_id' => 0, 'status' => 'active');
        $sub_coments = $this->model->withWhereGet($sub_post_data,array('user'));

        if(count($sub_coments) > 0) {
          foreach ($sub_coments as $key => $val) {
            $subData = array();
            $subreply = array();
            $subData['id'] = $val->id;
            $subData['comment'] = $val->comment;
            $subData['pre_comment'][$val->id] = $val->comment;
            $subData['time'] = $this->timeAgo($val->created_at);
            $subData['user'] = $val->user->nickname != ''? $val->user->nickname:$val->user->first_name.' '.$val->user->lst_name;
            $subData['profile_pic'] = $val->user->avatar;
            $subData['action'] = $val->user_id == Auth::user()->id?1:0;
            $subData['edit'] = false;
            $subData['reply'] = false;
            

            // add 2nd lavel reply
              $sub_reply_data = array('sub_parent_id' => $val->id, 'sub_sub_parent_id' => 0, 'status' => 'active');
              $sub_reply = $this->model->withWhereGet($sub_reply_data,array('user'));
              if(count($sub_reply) > 0) {
                foreach ($sub_reply as $key => $va) {
                  $subSubReply = array();
                  $subRData = array();
                  $subRData['id'] = $va->id;
                  $subRData['comment'] = $va->comment;
                  $subRData['pre_comment'][$va->id] = $va->comment;
                  $subRData['time'] = $this->timeAgo($va->created_at);
                  $subRData['user'] = $va->user->nickname != ''?$va->user->nickname:$va->user->first_name.' '.$va->user->lst_name;
                  $subRData['profile_pic'] = $va->user->avatar;
                  $subRData['action'] = $va->user_id == Auth::user()->id?1:0;
                  $subRData['edit'] = false;
                  $subRData['reply'] = false;
                  

                    // add 3rd lavel reply
                    $sub_sub_reply = array('sub_sub_parent_id' => $va->id, 'status' => 'active');
                    $sub_sub_reply = $this->model->withWhereGet($sub_sub_reply,array('user'));
                    if(count($sub_sub_reply) > 0) {
                      foreach ($sub_sub_reply as $key => $v) {
                        $subReply = array();
                        $subReply['id'] = $v->id;
                        $subReply['comment'] = $v->comment;
                        $subReply['pre_comment'][$v->id] = $v->comment;
                        $subReply['time'] = $this->timeAgo($v->created_at);
                        $subReply['user'] = $v->user->nickname != ''?$v->user->nickname:$v->user->first_name.' '.$v->user->lst_name;
                        $subReply['profile_pic'] = $v->user->avatar;
                        $subReply['action'] = $v->user_id == Auth::user()->id?1:0;
                        $subReply['edit'] = false;
                        $subReply['reply'] = false;
                        $subSubReply[] = $subReply;
                      }
                    } 
                    $subRData['subReply'] = $subSubReply;
                    $subreply[] = $subRData;
                    // add 3rd lavel reply
                }
              } 
              $subData['replies'] = $subreply;
              $subComments[] = $subData;
            // add 2nd lavel reply
          }
        } 

        $data['other'] = $subComments;
        $data['leval'] = 0;
        // $data['leval'] = count($sub_coments) == 2?1:0;
        $comments[] = $data;
      }

     

      return response()->json(new JsonResponse(['items' => $comments, 'status' => 'success']));
    }

  /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'comment' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }
        else {

          $UsersNameArray = array();
          $iOne = array_combine(range(1, count($request->comment)), array_values($request->comment));
          $count_comment = count($iOne);

          $getstring = explode(' ',$iOne[$count_comment]); 
          foreach ($getstring as $key => $value) {
              // echo $value;

              $len = strlen('@'); 
              $newstring = substr($value, 0, $len) === '@'; 
              if($newstring){
                $value = ltrim($value, '@');
                $UsersNameArray[] = $value;
              }else{
                echo "false";
              }
          }
          // foreach($UsersNameArray as $userName){
          //   $user = User::where('first_name',$userName)->first();
          //   echo $user->id;
          // }
          


            $data = $request->all(); 
            // print_r($data); die;
            $data['user_id'] = Auth::user()->id;
            $data['parent_comment_id'] = $request->comment_id;
            if($request->sub_parent_id > 0 && $request->sub_sub_parent_id == 0) {
              //$data['comment'] = $request->comment[$request->sub_parent_id];

              if(isset($request->comment[$request->sub_parent_id])){

                $data['comment'] = $request->comment[$request->sub_parent_id];
              }
              else{
                return response()->json(new JsonResponse(['status' => 'success']));
              }

            } 
            else if($request->sub_parent_id > 0 && $request->sub_sub_parent_id > 0) {
              if(isset($request->comment[$request->sub_sub_parent_id])){
                $data['comment'] = $request->comment[$request->sub_sub_parent_id];
              }
              else{

                return response()->json(new JsonResponse(['status' => 'success']));
              }
            }
            else {
              if(isset($request->comment[$request->comment_id])){

                $data['comment'] = $request->comment[$request->comment_id];
              }
              else{
                return response()->json(new JsonResponse(['status' => 'success']));
              }
            }
            $this->model->create($data);

            $this->addLeagueUser($data['league_id'], $data['round_id']);
            $this->updateCommentCounter($data['league_id'], $data['round_id']);
            if(count($request->userids) > 0) {
              foreach($request->userids as $u) {
                $this->triggerNotification($u,$data['league_id']);
              }
            }
           
            return response()->json(new JsonResponse(['status' => 'success']));
        }
        
    }



      /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function addLeagueUser($leaugeId,$roundId)
    {
     $post_data = array('id' => $leaugeId);
      $league = $this->league->withWhereGet($post_data,array('getUsers'))->toArray();

      // print_r($league);

      foreach ($league[0]['get_users'] as $key => $u) {

        $check_exit = LeagueCommentStatus::where('league_id',$leaugeId)->where('round_id',$roundId)->where('user_id',$u['user_id'])->first();
         
        if(!$check_exit) {
            $data = array(); 
            $data['user_id'] = $u['user_id'];
            $data['league_id'] = $leaugeId;
            $data['round_id'] = $roundId;
            $data['comment_count'] = 0;
            $this->commentCounter->create($data);  
        }
        
      }


    }

          /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function updateCommentCounter($leaugeId,$roundId)
    {


     $post_data = array('id' => $leaugeId);
      $league = $this->league->withWhereGet($post_data,array('getUsers'))->toArray();
      foreach ($league[0]['get_users'] as $key => $u) {
        if(Auth::user()->id != $u['user_id']) {
          $check_exit = LeagueCommentStatus::where('league_id',$leaugeId)->where('round_id',$roundId)->where('user_id',$u['user_id'])->first();
          $data = array(); 
          $data['comment_count'] = $check_exit->comment_count + 1;
          $this->commentCounter->update($check_exit->id, $data);
        }
      }

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
      $data['comment'] = $request->pre_comment[$request->comment_id];
      $this->model->update($request->comment_id, $data);
      return response()->json(new JsonResponse(['status' => 'success']));
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $this->model->update($id, array('status' => 'deactive'));
      return response()->json(new JsonResponse(['status' => 'success']));
    }

     /**
     * Its used to show Comment history time
     * @param  \Illuminate\Http\Request  $time_ago
     * @return \1m,1h type time
     */
     private function timeAgo($time_ago)
      {
        $time_ago = strtotime($time_ago);
        $cur_time   = time();
        
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        // Seconds
        if($seconds <= 60){
            return "just now";
        }
        //Minutes
        else if($minutes <=60){
            if($minutes==1){
                return "1m";
            }
            else{
                return $minutes.'m';
            }
        }
        //Hours
        else if($hours <=24){
            if($hours==1){
                return "1h";
            }else{
                return $hours.'h';
            }
        }
        //Days
        else if($days <= 7){
            if($days==1){
                return "1 day";
            }else{
                return $days.'d';
            }
        }
        //Weeks
        else if($weeks <= 4.3){
            if($weeks==1){
                return "1 week";
            }else{
                return "$weeks weeks";
            }
        }
        //Months
        else if($months <=12){
            if($months==1){
                return "1 month";
            }else{
                return "$months months";
            }
        }
        //Years
        else{
            if($years==1){
                return "1 year";
            }else{
                return "$years years";
            }
        }
           
      }

        /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function resetCounter($leaugeId)
    {
       
        $check_exit = LeagueCommentStatus::where('league_id',$leaugeId)->where('user_id',Auth::user()->id)->first();
        if($check_exit) {
          $data = array(); 
          $data['comment_count'] = 0;
          $this->commentCounter->update($check_exit->id, $data);
        }
          
      return response()->json(new JsonResponse(['status' => 'success']));
    }

    public function triggerNotification($user_id,$league_id) {
           $constantArray = array('{{userName}}');
           $uname=  ucfirst(Auth::user()->nickname != ''?Auth::user()->nickname:Auth::user()->first_name.' '.Auth::user()->last_name);
             $valueArray = array($uname);
                $notify = array(
                    'slug' => 'tag_user_in_comment',
                    'userId' => $user_id,
                    'type' => 'comment',
                    'targetTable' => 'users',
                    'targetCol' => 'avatar',
                    'leagueId' => $league_id,
                    'targetId' => Auth::user()->id
                  );
                  NotificationHandler::save($constantArray , $valueArray, $notify);
             
    }

 
}
