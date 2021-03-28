<?php
/**
 * File AuthController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Http\Controllers;

use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Laravue\Models\PermissionGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use Modules\Frontend\Models\League;
use Modules\Frontend\Models\Invite;
use Mail; 

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        
        $leagueId = $request['userInfo']['leagueId'];
         if($leagueId && $leagueId != '') {
            $request['referral_league'] = base64_decode($leagueId);
            $invite_id = base64_decode($request['userInfo']['inviteId']);

            $check_league = League::find($request['referral_league']);
            if($check_league) {
                $invite = Invite::where('id',$invite_id)->where('leagues_id',$request['referral_league'])->first();
                // if($invite) {

                //     // if($invite->email != $request['email']) {
                //     //     return response(['errors'=> array('Your email is not match with invite link.')], 422);
                //     //      exit;
                //     // }


                // } else {
                //     return response(['errors'=> array('Url is invalid')], 422);
                //     exit;
                // }
            } else {
                    return response(['errors'=> array('Url is invalid')], 422);
                    exit;
            }
        }

        $data = $request->all();

        $credentials = array('email' =>$data['email'], 'password' =>$data['password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(new JsonResponse([], 'Your email address and password don’t match. Please check and try again.'), Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();

        

        $token = $user->createToken('laravue');

       

        return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
    }

    public function register(Request $request)
    {

        if($request['leagueId'] && $request['leagueId'] != '') {
            $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            ]);
        }

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);

        $Getpermission = PermissionGroup::where('id',2)->first();

        $request['access_permissions'] = $Getpermission['permission_ids'];
        $request['is_mark_admin'] = 'no';
        $request['is_marketing_option_active'] = 'yes';
        $request['is_notifications_option_active'] = 'yes';
        // die("here");
        if($request['leagueId'] && $request['leagueId'] != '') {
            $request['referral_league'] = base64_decode($request['leagueId']);
            $invite_id = base64_decode($request['inviteId']);

            $check_league = League::find($request['referral_league']);
            if($check_league) {
                $invite = Invite::where('id',$invite_id)->where('leagues_id',$request['referral_league'])->first();
                if($invite || $request['mode'] == 'fb' || $request['mode'] == 'tw') {

                    // if($invite->email != $request['email']) {
                    //      return response(['errors'=> array('Your email is not match with invite link.')], 422);
                    // }

                } else {
                    return response(['errors'=> array('Url is invalid')], 422);
                }
            } else {
                    return response(['errors'=> array('Url is invalid')], 422);
            }

            $user = User::where('email',$request->email)->first();

            if($user) {
                User::where('email',$request->email)->update(['password' => $request['password']]);
            } else {
                $user = User::create($request->toArray());
            }

        } else {
            $user = User::create($request->toArray());
        }


        // $user = User::create($request->toArray());

        Auth::loginUsingId($user->id);
        $this->WelcomeEmail($user->id);
        $user = $request->user();
        $token = $user->createToken('laravue');
        return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
    }

public function signupuser(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $request['password']=Hash::make($request['password']);
        
        if($request['leagueId'] && $request['leagueId'] != '') {
            $request['referral_league'] = base64_decode($request['leagueId']);
        }
        
        $user = User::create($request->toArray());
        Auth::loginUsingId($user->id);
        $this->WelcomeEmail($user->id);
        $user = $request->user();

        $token = $user->createToken('laravue');

        return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json((new JsonResponse())->success([]), Response::HTTP_OK);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findEmail(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        if($user) {
            return response()->json(new JsonResponse(['status' => 'yes']));
        } else {
            return response()->json(new JsonResponse(['status' => 'no']));
        }
    }

    public function WelcomeEmail($user_id){
        $userdata = User::where('id',$user_id)->first();
        // print_r($user_id);
        $data['name'] = 'Hello, '. $userdata->first_name.' '.$userdata->last_name;
            $data['content'] = ' Welcome to LMS ';
            $data['to'] = $userdata->email;
            $subject = 'Welcome';

            Mail::send('emails.welcome', $data, function($message) use ($data) {
                     $message->to($data['to'], 'Welcome')->subject
                        ('Welcome');
                  });
    }
}
