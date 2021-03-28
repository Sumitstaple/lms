<?php

namespace App\Http\Middleware;
use App\Laravue\Models\Permission;
use Illuminate\Support\Facades\Auth;

use Closure;

class Admin
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

        // return $user  = Auth::user();

        return $next($request);
    }
}
