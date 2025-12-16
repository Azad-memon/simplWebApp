<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
   public function handle($request, Closure $next, ...$role)
{

   // dd($role);
    if (auth()->check() && in_array(auth()->user()->role->name, $role)) {
        return $next($request);
    }
    //Auth::logout();
    abort(403, 'Unauthorized');
}
}
