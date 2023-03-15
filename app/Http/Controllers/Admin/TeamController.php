<?php

namespace App\Http\Controllers\Admin;

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
use TwitchApi;
use App\Models\BoomMeter;
use App\Models\BoomMeterType;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $teamname = $request->input("search");
        if(isset($teamname) && $teamname != "")
        {
            $teams = Team::where("name", $teamname)->paginate(50);
        }
        else
        {
            $teams = Team::orderby("created_at","desc")->paginate(50);
        }
        
        return view('admin.team.index', ["teams" => $teams]);
    }

    public function addOrUpdateView(Request $request)
    {
        $teamId = $request->input("id");
        $teamname = "";
        $users = null;
        $isEdit = 0;
        $banner = "";
        $ownername = "";
        $usersTeam = "";
        $links = ["website" => "", "twitch_link" => "",
                  "twitter_link" => "", "facebook_link" => ""];
        if($teamId != "" && $teamId != null)
        {
            $isEdit = 1;
            $team = Team::find($teamId);
            $teamname = $team->name;
            $owner = User::find($team->owner_id);
            if($owner != null)
            {
                $ownername = $owner->name;
            }
            $users = User::where("team_id", $teamId)->get();
            $list = User::where("team_id", $teamId)->pluck("name")->toArray();
            if(count($list) >= 1)
            {
                $usersTeam = implode(",", $list);
            }
            $banner = config("content.cloudfront_f").$team->banner_link;
            $links["website"] = $team->website;
            $links["twitch_link"] = $team->twitch_link;
            $links["twitter_link"] = $team->twitter_link;
            $links["facebook_link"] = $team->facebook_link;
        }
        $boommeters = BoomMeterType::all();
        $folderServer = strtolower(config("aws.folder_client"));
        $thumnailBoomMt = config("aws.linkS3BoomMeter") . $folderServer . "/";
        return view('admin.team.add', 
        ["isEdit" => $isEdit, "teamname" => $teamname, "users" => $users,
        "banner" => $banner, "ownername" => $ownername, 
        "teamId" => $teamId, "usersTeam" => $usersTeam,
        "boommeters" => $boommeters, "thumnailBoomMt" => $thumnailBoomMt,
        "links" => $links]);
    }

    public function addOrUpdate(Request $request)
    {
        $teamname = $request->input("teamname");
        $ownername = $request->input("is_owner");
        $users = $request->input("users_team");
        $teamId = $request->input("id");
        $website = $request->input("website");
        $twitch_link = $request->input("twitch_link");
        $twitter_link = $request->input("twitter_link");
        $facebook_link = $request->input("facebook_link");
        if($teamname == null || $teamname == "")
        {
            return Redirect::back()->withErrors(['Requice team name']);
        }
        $team = Team::where("name", $teamname)->first();
        if($team != null && !is_numeric($teamId))
        {
            return Redirect::back()->withErrors(['Teamname existed']);
        }
        if($team == null) 
        {
            $team = new Team();
            $team->name = $teamname;
        }
        $owner = User::where("name", $ownername)
                ->where("type", User::USER_TYPE_TWITCH)->first();
        if($owner != null)
        {
            $team->owner_id = $owner->id;
        }
        $team->website = $website;
        $team->twitch_link = $twitch_link;
        $team->twitter_link = $twitter_link;
        $team->facebook_link = $facebook_link;
        $team->save();

        $file = $request->file("banner");

        if ($request->hasFile("banner")) 
        {
            Log::info("has banner");
            Team::uploadBanner($file, $team->id);
        }
        else Log::info("no banner");

        $usersArr = explode(",", $users);
        Team::updateTeamForUser($usersArr, $team->id);

        return Redirect::route('admin.team');
    }

    public function getUsersTeam(Request $request)
    {
        try {
        $ts = TwitchApi::teams();
        $teamname = $request->input("teamname");
        $usersTwitch = TwitchApi::team($teamname)["users"];
        $nameTwitch = array_pluck($usersTwitch, "name");
        //return $nameTwitch;
        $users = User::whereIn("name", $nameTwitch)
                 ->where("type", User::USER_TYPE_TWITCH)->get();
            return response()->json(["status" => 0, "users" => $users]);
        } 
        catch(\Exception $e)
        {
            Log::info("get team form twitch error");
            Log::info($e);
        }
        return response()->json(["status" => 1, "error" => "not found"]);
    }
    public function delete(Request $request)
    {
        $id = $request->input("id");
        Team::deleteTeam($id);
        return Redirect::route('admin.team');
    }
    public function searchUser(Request $request)
    {
        $term = $request->input("term");
        $usernames = User::where("name", "like", "%".$term."%")
                    ->where("type", User::USER_TYPE_TWITCH)->pluck("name");
        return json_encode($usernames);
    }
}