<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Auth;
use App\Models\UserAdminPermission;
use Session;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $guard = 'admin')
    {
        if (Auth::guard($guard)->guest()) {
            $is_user_login_admin = Session::get('is_user_login_admin');
            if ($is_user_login_admin){
                $f_user = Auth::user();
                if ($f_user){
                    $user_admin_permission = UserAdminPermission::where('user_id',$f_user->id)->get();
                    if (count($user_admin_permission)){
                        Auth::guard($guard)->login($f_user);
                        Session::put('_is_f_user',"web");
                        return $next($request);
                    }
                }
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect(route("admin.login"));
            }
        }

        return $next($request);
    }
}
