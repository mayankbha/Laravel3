<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use File;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Team;
use App\Helpers\Helper;
use Redirect;
use Log;
use Auth;

class TeamController extends Controller
{
    public function team(Request $request, $teamname)
    {
        $team = Team::where("name", $teamname)->first();
        if($team != null)
        {
            $banner = config("content.cloudfront_f").$team->banner_link;
            $userArr = User::where("team_id", $team->id)->pluck("id")->toArray();
            $members = SocialAccount::whereIn("user_id", $userArr)
                      ->orderBy("subscriber_numb", "desc")->get();
            $totalUser = $members->count();
            return  view('team.index', 
            ["team" => $team, "members" => $members, "total" => $totalUser,
             "banner" => $banner]);
        }
        else
        {
            abort(404);
        }
        
    }
    public function changeBanner(Request $request)
    {
        $teamname = $request->input('teamname');
        $website = $request->input('website');
        $twitch_link = $request->input('twitch_link');
        $twitter_link = $request->input('twitter_link');
        $facebook_link = $request->input('facebook_link');
        $team = Team::where("name", $teamname)->first();
        if($team != null && Auth::id() == $team->owner_id)
        {
            $team->website = $website;
            $team->twitch_link = $twitch_link;
            $team->twitter_link = $twitter_link;
            $team->facebook_link = $facebook_link;
            $team->save();
            $file = $request->file("banner");
            if ($request->hasFile("banner")) 
            {
                Team::uploadBanner($file, $team->id);
            }
        }
        return redirect()->to(route('team',["name" => $teamname]));
    }
}