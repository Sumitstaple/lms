<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Laravue\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Laravue\Models\PermissionGroup;
use Mail; 
use App\SocialIdentity;
use App\Services\SocialTwitterAccountService;
use Storage;

class SocialController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    public function index()
    {
      return view('welcome');
    }

    public function SocialLogin($provider, Request $request)
    {

        if($provider == 'twitter') {
            $redirectRes = Socialite::driver('twitter')->redirect();
            $redirectResArr = (array)$redirectRes;
            $url = '';
            $i = 1;
            foreach ($redirectResArr as $key => $value) {
                if($i == 3){
                    $urlString = $value;
                }
                $i++;
            }
            $url = parse_url( $urlString, $component = -1 );
            $ss = preg_match_all("/=([^\s]+)/", $url['query'], $matches);
            $oauth_token = $matches[1][0];
 
        return response()->json(array('oauth_token' => $oauth_token));

         } else if($provider == 'facebook') { 
             $providerUser = Socialite::driver($provider)->stateless()->user();

             $account = SocialIdentity::where('provider',$provider)
            ->where('provider_user_id',$providerUser->id)
            ->first();

        if ($account) {
                $user = $account->user;
        } else {

            $account = new SocialIdentity([
                'provider_user_id' => $providerUser->id,
                'provider' => $provider,
            ]);
          
            if($providerUser->email){
                $user = User::whereEmail($providerUser->email)->first();
                if(!$user){
                    $getpermission = PermissionGroup::where('id',2)->first();
                    $user = User::create([
                    'email' => $providerUser->email,
                    'first_name' => $providerUser->name,
                    'last_name' => null,
                    'password' => $providerUser->id,
                    'access_permissions' => $getpermission['permission_ids'],
                ]); 
                }
            } else {
                $getpermission = PermissionGroup::where('id',2)->first();
                    $user = User::create([
                    'email' => $providerUser->email,
                    'first_name' => $providerUser->name,
                    'last_name' => null,
                    'password' => $providerUser->id,
                    'access_permissions' => $getpermission['permission_ids'],
                ]); 
            }
            
            $account->user()->associate($user);
            $account->save();
        }

        Auth::loginUsingId($user->id);
        if(Auth::check()){
            $user = Auth::user();
            $user = $request->user();
            $token = $user->createToken('laravue');
            return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
        }
    } else {
        $user = Socialite::driver($provider)->stateless()->user();
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            if (Auth::loginUsingId($existingUser->id)) {
                $user = $request->user();
                $token = $user->createToken('laravue');
                return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
            }
        }
        else{
            return response()->json(array('status'=>'signup','user'=>$user));
        }
    }
        
    }

    public function SocialSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        // 'post_code' => 'required',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $request['password']=Hash::make($request['email']);

         $Getpermission = PermissionGroup::where('id',2)->first();

        $request['access_permissions'] = $Getpermission['permission_ids'];
        $request['is_mark_admin'] = 'no';

        $user = User::create($request->toArray());
        Auth::loginUsingId($user->id);
        $this->WelcomeEmail($user->id);
        $user = $request->user();
        $token = $user->createToken('laravue');

        return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
    }

    public function user()
    {
        return new UserResource(Auth::user());
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

    public function callback(SocialTwitterAccountService $service,$provider)
    {
        if($provider == 'twitter'){
            $oauth_token = $_GET['oauth_token'];
            $oauth_verifier = $_GET['oauth_verifier'];
            $user = $service->createOrGetUser(Socialite::driver('twitter')->user(),$oauth_token,$oauth_verifier);
        } else {
            return $provider;
        }
    }

   

    public function checklogin(Request $request)
    {
        $json = $request->config['data'];
        $json_data = json_decode($json);
        $oauth_verifier = $json_data->oauth_verifier;
        $oauth_token = $json_data->oauth_token;
        $token = $oauth_token.'_'.$oauth_verifier;
        $account = SocialIdentity::where('token',$token)->first();
        Auth::loginUsingId($account->user_id);
        if(Auth::check()){
            $user = Auth::user();
            $user = $request->user();
            $token = $user->createToken('laravue');
            return response()->json(new UserResource($user), Response::HTTP_OK)->header('Authorization', $token->plainTextToken);
        }
        
    }

    public function callback1()
    {
        // $user = array("hello","uiop");
        // $user = json_encode($user);
        // Storage::put('attempt1.txt', $user);

        echo date("y-m-d h:i:s");
    }

     public function callback2()
    {
       
        $data['name'] = 'Hello';
            $data['content'] = ' Welcome to LMS ';
            $data['to'] = 'gurpreet@staplelogic.in';
            $subject = 'Welcome to live';

            Mail::send('emails.leaguecreate', $data, function($message) use ($data) {
                     $message->to($data['to'], 'Welcome')->subject
                        ('Welcome to lms');
                  });
    }
}
