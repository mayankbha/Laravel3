<?php

namespace App\Http\Controllers;

use App\Models\UserAdminPermission;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use View;
use App\Models\Setting;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $urlAccess = config('content.cloudfront') . '/design/' . config('content.design_ver') . '/asset';
        View::share('urlAccess', $urlAccess);
        $boom_setting = Setting::all_setting();
        view()->share('boom_setting',$boom_setting);
        $this->boom_setting = $boom_setting;
        if (auth()->id()){
            $is_admin_user = UserAdminPermission::where('user_id',auth()->id())->first();
            if ($is_admin_user){
                view()->share('is_admin_user',1);
                $this->is_admin_user = 1;
            }
            else{
                view()->share('is_admin_user',0);
                $this->is_admin_user = 0;
            }
        }
    }
}
