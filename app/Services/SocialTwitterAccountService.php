<?php

namespace App\Services;
use App\SocialIdentity;
use App\User;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Illuminate\Http\Response;
use App\Laravue\Models\PermissionGroup;

class SocialTwitterAccountService
{
    public function createOrGetUser(ProviderUser $providerUser,$oauth_token,$oauth_verifier)
    {
        $account = SocialIdentity::where('provider','twitter')
            ->where('provider_user_id',$providerUser->getId())
            ->first();

        if ($account) {
            SocialIdentity::where('provider','twitter')
            ->where('provider_user_id',$providerUser->getId())->update(['token' => $oauth_token.'_'.$oauth_verifier]);
            return $account->user;
        } else {

            $account = new SocialIdentity([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'twitter',
                'token' => $oauth_token.'_'.$oauth_verifier,
            ]);
          
            if($providerUser->getEmail()){
                $user = User::whereEmail($providerUser->getEmail())->first();
                if(!$user){
                    $getpermission = PermissionGroup::where('id',2)->first();
                    $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'first_name' => $providerUser->getName(),
                    'last_name' => null,
                    'password' => $oauth_token.'='.$oauth_verifier,
                    'access_permissions' => $getpermission['permission_ids'],
                ]); 
                }
            } else {
                $getpermission = PermissionGroup::where('id',2)->first();
                    $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'first_name' => $providerUser->getName(),
                    'last_name' => null,
                    'password' => $oauth_token.'='.$oauth_verifier,
                    'access_permissions' => $getpermission['permission_ids'],
                ]); 
            }
            
            $account->user()->associate($user);
            $account->save();
            return $providerUser;
        }
    }
}