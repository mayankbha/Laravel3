<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\UnsubscriberEmail;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\User;
use App\Models\Video;
use App\Models\Game;
use App\Models\SocialAccount;
use App\Models\UnsubcriberEmail;
use App\Models\Subscription;

use Auth;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Lang;
use Redis;
use Cache;

class UserController extends Controller
{
    //

    public function myProfile(){

        $total = config('view.page_index');
        $gameCategory = Game::get_category_game();
        $game_acc_array = explode(",",Auth::user()->game_accounts);

        Auth::user()->steam = isset($game_acc_array[0]) ? $game_acc_array[0] : "";
        Auth::user()->battle = isset($game_acc_array[1]) ? $game_acc_array[1] : "";
        Auth::user()->lol = isset($game_acc_array[2]) ? $game_acc_array[2] : "";
        $user_extend_info  = SocialAccount::Where('user_id',Auth::id())->get()->first();

        $trending = Video::fiterBy(Video::FILTER_TRENDING);
        $my_video = Video::my_video();
        $userGameList = Game::getUserGameList(auth()->user());

        $discord_msg = Session::pull('discord_msg');
        if ($discord_msg == null){
            $discord_msg = "";
        }
        $user = Auth::user();
        $type = "twitch";
        $linkProfile = "https://www.twitch.tv/".$user->name;
        if($user->type == User::USER_TYPE_MIXER)
        {
            $type = "mixer";
            $linkProfile = "https://mixer.com/".$user->name;
        }
        if($user->type == User::USER_TYPE_YOUTUBE)
        {
            $type = "youtube";
            $linkProfile = "https://www.youtube.com/channel/".$user_extend_info->channel_id."/live";
        }
        return view('user.profile',
            [
                "discord_msg"=>$discord_msg,
                "total" => $total,
                "trending" => $trending,
                "listgame" => $gameCategory,
                "userGameList" => $userGameList,
                "user_extend" => $user_extend_info,
                "my_video" => $my_video,
                "type" => $type,
                "linkProfile" => $linkProfile
            ]);
    }

    public function showProfile(Request $request,$name = "",$subscribe = ""){
        
        if (config('app.env') == "production"){
            if ($request->server('HTTP_X_FORWARDED_PROTO') == "http"){
                $rediect_uri = url()->current() . ( (\Request::getQueryString() != "") ? ("?" . \Request::getQueryString()) : "" );
                return redirect()->to($rediect_uri);
            }
        }
        $user = User::Where("name",$name)->get()->first();
        $ids = User::Where("name",$name)->pluck("id")->toArray();
        if (!$user){
            abort(404);
        }
        $type = "twitch";
        $linkProfile = "https://www.twitch.tv/".$user->name;
        if($user->type == User::USER_TYPE_MIXER)
        {
            $type = "mixer";
            $linkProfile = "https://mixer.com/".$user->name;
        }

        /*if ($user->id == Auth::id()){
            return $this->myProfile();
        }*/
        if (in_array(Auth::id(), $ids))
        {
            return $this->myProfile();
        }
        if(Auth::id()){
            $loggedin = true;
        }
        else {
            $loggedin = false;
        }

        $game_acc_array = explode(",",$user->game_accounts);

        $user->steam = isset($game_acc_array[0]) ? $game_acc_array[0] : "";
        $user->battle = isset($game_acc_array[1]) ? $game_acc_array[1] : "";
        $user->lol = isset($game_acc_array[2]) ? $game_acc_array[2] : "";

        $total = config('view.page_index');
        $gameCategory = Game::get_category_game();
        $userGameList = Game::getUserGameList($user);
        $user_extend_info  = SocialAccount::Where('user_id',$user->id)->get()->first();

        if($user->type == User::USER_TYPE_YOUTUBE)
        {
            $type = "youtube";
            $linkProfile = "https://www.youtube.com/channel/".$user_extend_info->channel_id."/live";
        }
        
        $trending = Video::fiterBy(Video::FILTER_TRENDING);
        $showClaim = true;
        if($user->is_claim == 1) $showClaim = false;

        $subscribed = "";
        if(Auth::id()){
            $subscription = Subscription::Where(array("streamer_id"=>$user->id, "subscriber_id"=>Auth::id()))->get()->first();
            if($subscribe == "subscribe"){
                if(sizeof($subscription)==0){
                    $subscription = new Subscription();
                    $subscription->streamer_id=$user->id;
                    $subscription->subscriber_id=Auth::id();
                    $subscription->status=Subscription::SUBSCRIBED;
                    $subscription->save();
                    Cache::flush();
                }
                else if(sizeof($subscription)>0 && $subscription->status == Subscription::UNSUBSCRIBED){
                    $subscription->status=Subscription::SUBSCRIBED;
                    $subscription->save();
                    Cache::flush();
                }
                $subscribed = 1;
            }
            else if(sizeof($subscription)>0 && $subscription->status == Subscription::SUBSCRIBED) {
                $subscribed = 1;
            }
            else {
                $subscribed = 0;
            }
        }
                
        return view('user.show_profile',
            [
                "total" => $total,
                "trending" => $trending,
                "listgame" => $gameCategory,
                "userGameList" => $userGameList,
                "user_extend" => $user_extend_info,
                "user" => $user,
                "showClaim" => $showClaim,
                "type" => $type,
                "linkProfile" => $linkProfile,
                "subscribed" => $subscribed,
                "subscribe" => $subscribe,
                "loggedin" => $loggedin,
            ]);
    }

    public function saveProfile(Request $request){

        $this->validate($request,[
            "steam" => "max:" . config('input.profile.game_account'),
            "battle" => "max:" . config('input.profile.game_account'),
            "lol" => "max:" . config('input.profile.game_account'),
            "facebook" => "max:" . config('input.profile.social_username'),
            "twitter" => "max:" . config('input.profile.social_username'),
            "reddit" => "max:" . config('input.profile.social_username'),
            "des" => "max:" . config('input.profile.description'),
        ]);

        $steam = trim($request->input('steam'));
        $battle = trim($request->input('battle'));
        $lol = trim($request->input('lol'));
        $des = trim($request->input('des'));

        $facebook = trim($request->input('facebook'));
        $twitter = trim($request->input('twitter'));
        $reddit = trim($request->input('reddit'));

        $game_account = implode(",",[$steam,$battle,$lol]);
        $return = User::Where('id',Auth::id())->update([
            'game_accounts'=>$game_account,
            'des'=>$des,
            'facebook_link'=>$facebook,
            'twitter_link'=>$twitter,
            'reddit_link'=>$reddit,
        ]);
        $user = User::Where('id',Auth::id())->get()->first();
        $game_acc_array = explode(",",$user->game_accounts);

        $user->steam = isset($game_acc_array[0]) ? $game_acc_array[0] : "";
        $user->battle = isset($game_acc_array[1]) ? $game_acc_array[1] : "";
        $user->lol = isset($game_acc_array[2]) ? $game_acc_array[2] : "";
        if ($return)
            return response()->json(['state' => $return, 'msg' => Lang::get('user.success_update_profile'),'user'=>$user->toJson()]);
        else
            return response()->json(['state' => $return, 'msg' => Lang::get('user.error_update_profile')]);

    }

    public function unsubscribeEmail(Request $request){
        $code = $request->input('code');
        $key = $request->input('key');
        $type = $request->input('type');
        if ($type == null){
            $type = 0;
        }
        if (!in_array($type,[UnsubscriberEmail::TYPE_MONTAGE,UnsubscriberEmail::TYPE_CHURN])){
            return redirect()->to(route('ahome'));
        }

        if (!UnsubscriberEmail::checkValidateKey($code,$key)){
            return redirect()->to(route('ahome'));
        }

        $user = User::where('code',$code)->first();
        if (!$user){
            return redirect()->to(route('ahome'));
        }
        $unsubcriberEmail = UnsubscriberEmail::where('user_id',$user->id)->where('type',$type)->first();
        if (!$unsubcriberEmail){
            $unsubcriberEmail = new UnsubscriberEmail();
            $unsubcriberEmail->email = $user->email;
            $unsubcriberEmail->user_id = $user->id;
            $unsubcriberEmail->status = 1;
            $unsubcriberEmail->type = $type;
            $unsubcriberEmail->save();
        }
        return view("user.unsubscriber_email");
    }
}
