<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;
use Route;

class RoleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // 判斷是否登入
        if (!Auth::guard($guard)->check() && !Auth::guard($guard)->viaRemember()) {
            return redirect()->route("{$guard}.login");
        }

        $request->offsetSet('auth', Auth::guard($guard)->user());

        return $next($request);
    }
}
