<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Video;
use App\Models\SocialAccount;
use App\Models\SocialFollows;

use Auth;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Lang;
use Redis;
use TwitchApi;
use Cache;

class SubscriptionsController extends Controller
{
    public function index(Request $request){

        $subscribed = Subscription::with('streamer')->where(array("subscriber_id"=>Auth::id(), "status"=>Subscription::SUBSCRIBED))->get();

        $recommended = array();
        $recommendedData = SocialFollows::with('recommendedstreamer')->Where(array("user_id"=>Auth::id()))->get();
        if(!empty($recommendedData) && sizeof(($recommendedData))>0 && isset($recommendedData[0]["id"]) && isset($recommendedData[0]["updated_at"]) && strtotime($recommendedData[0]["updated_at"])>strtotime('-5 days')){
            foreach($recommendedData as $key=>$data){
                $alreadySubscribed = Subscription::Where(array('streamer_id'=>$data['recommended_streamer_id'],'subscriber_id'=>$data['user_id']))->get()->first();
                if(empty($alreadySubscribed)){
                    $recommended[$key]['id'] = $data['recommended_streamer_id'];
                    $recommended[$key]['name'] = $data['recommendedstreamer']['name'];
                    $recommended[$key]['avatar'] = $data['recommendedstreamer']['avatar'];
                    $recommended[$key]['followers'] = $data['followers'];
                }
            }
        }
        else {
            $social = SocialAccount::Where('user_id', Auth::id())->get()->first();
            $options = ['limit' => 0];
            $twitchFollowings = TwitchApi::followings($social->social_id, $options);
			$twitchFollowings = (array)$twitchFollowings;
			if(!empty($twitchFollowings) && isset($twitchFollowings['follows'])){
                foreach($twitchFollowings['follows']  as $key => $twitch_user) {
                    // Check if Twitch user exists in boomtv via Twitch name   
                    $user = User::Where("name", $twitch_user['channel']['name'])->get()->first();
                    if($user){
                        $subscription = Subscription::Where(array("streamer_id"=>$user->id,"subscriber_id"=>Auth::id()))->get()->first();
                        if(!empty($user) && empty($subscription)) {
                            $recommended[$key]['id'] = $user['id'];
                            $recommended[$key]['name'] = $twitch_user['channel']['name'];
                            $recommended[$key]['avatar'] = $twitch_user['channel']['logo'];
                            $recommended[$key]['followers'] = $twitch_user['channel']['followers'];
                        }
                    }
                }
                // Sort recommended array by followers in descending order  
                usort($recommended, function($a, $b) {
                    return $b['followers'] - $a['followers'];  
                });  
                $recommended = array_slice($recommended, 0, 5);

                // Adding new record in DB
                SocialFollows::Where(array("user_id"=>Auth::id()))->delete();        
                foreach($recommended as $val){
                    $twitchFollows = new SocialFollows();
                    $twitchFollows->user_id = Auth::id();
                    $twitchFollows->social_account_type = User::USER_TYPE_TWITCH;
                    $twitchFollows->recommended_streamer_id = $val['id'];
                    $twitchFollows->followers = $val['followers'];
                    $twitchFollows->save();
                }
            }
        }

        return view('subscriptions.index',
            [
                "subscribed" => $subscribed,
                "recommended" => $recommended,
            ]);
    }
    
    public function unsubscribe(Request $request){
        $subscription = Subscription::with('streamer')->where(array("streamer_id"=>$request->input('streamer_id'), "subscriber_id"=>Auth::id(), "status"=>Subscription::SUBSCRIBED))->first();
        $subscription->status=0;
        $subscription->save();
        Cache::flush();
        Session::flash('message', 'You have unsubscribed '.$subscription->streamer->name.'!');
        Session::flash('alert-class', 'notifications_success');
        return response()->json(['status'=>1]);
    }

    public function subscribe(Request $request){
        $subscription = Subscription::Where(array("streamer_id"=>$request->input('streamer_id'), "subscriber_id"=>Auth::id()))->get()->first();
        if(sizeof($subscription)==0){
            $subscription = new Subscription();
            $subscription->streamer_id=$request->input('streamer_id');
            $subscription->subscriber_id=Auth::id();
            $subscription->status=1;
            $subscription->save();
        }
        else if(sizeof($subscription)>0 && $subscription->status == Subscription::UNSUBSCRIBED){
            $subscription->status=Subscription::SUBSCRIBED;
            $subscription->save();
        }
        Cache::flush();
        return response()->json(['status'=>1]);
    }
}
