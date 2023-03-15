<?php

namespace App\Http\Middleware;

use Closure;
use Log;
class RedirectMiddleware
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
        $beta_domain=env('BETA_DOMAIN');
        $url=$request->url();
        $route=$request->route()->getName();
        $path=$request->getPathInfo();
        $querry=$request->getQueryString();
        $domain=substr($url, 0,strlen($beta_domain));
    
        
        if($route!="comingsoon" && $domain!=$beta_domain)
        {   $redirect="";
            if($querry!="")
                $redirect=$beta_domain.$path."?".$querry;
            else
                $redirect=$beta_domain.$path;
            return redirect($redirect);
        }

        return $next($request);
    }
    
}
