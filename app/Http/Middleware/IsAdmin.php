<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(Auth::check()){
            // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('staff')) {
            // }
    
            $user_id = Auth::user();
            

            $getAllPermissions = Permission::whereIn('id',array(2,3))->pluck('code');

            return $next($request);
        }
        
        
        // Auth::logout();
        // Session::flush();
        // return redirect('adminlogin');

        // return redirect('/');
    }
}
