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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use Mail;
use App\PasswordReset;
use Illuminate\Support\Str;
use Carbon\Carbon;
/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class ApiForgotPasswordController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $user = User::where('email', $request->email)->first(); 

            if (!$user)
                return response(['errors'=> 'We can not find a user with that e-mail address'], 422);
            $ispasswordReset = PasswordReset::where('email',$user->email)->first();

            if ($ispasswordReset) {
                PasswordReset::where('email',$user->email)->delete();
            }

            $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
             ]
            );
           
            if ($user && $passwordReset){
            	
            	$data = array(
            		 'link'=> url('/#/password/forgot/?token='.$passwordReset->token),
            		 'to' => $passwordReset->email
            		);  
                 
			      Mail::send('emails.resetPassword', $data, function($message) use ($data) {
			         $message->to($data['to'], 'Forgotten your password?')->subject
			            ('Reset Password Link');
			      });
			      return response(['success'=> 'We have e-mailed your password reset link!'], Response::HTTP_OK);
                //return response()->json(url('/#/password/forgot/?token='.$passwordReset->token));
            }
    }

    //    public function find($token)
    // {
    //     $passwordReset = PasswordReset::where('token', $token)->first();

    //         if (!$passwordReset)
    //         return response()->json([
    //             'message' => 'This password reset token is invalid.'
    //         ], 404);

    //         if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
    //         $passwordReset->delete();

    //         return response()->json([
    //             'message' => 'This password reset token is invalid.'
    //         ], 404);
    //         }
    //         return response()->json($passwordReset);
    // }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();


        if (!$passwordReset)
            return response(['errors'=> 'This password reset token is invalid'], 422);

        $user = User::where('email', $passwordReset->email)->first();
            if (!$user)
            return response(['errors'=> 'We cant find a user with that e-mail address'], 422);

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->delete();
        return response()->json($user);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }
}
