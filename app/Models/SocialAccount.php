<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TwitchApi;
use Log;
use GuzzleHttp\Client;
use App\Helpers\MixerHelper;

use Carbon\Carbon;

use Youtube;

class SocialAccount extends Model
{
    protected $table = 'social_accounts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'social_id', 'access_token', 'refresh_token', 'expires','type','created_at', 'updated_at', 'view_numb',
        'follower_numb', 'following_numb', 'subscriber_numb', 'token_type', 'channel_id', 'expire_in'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public static function updateProfile()
    {
        $accounts = SocialAccount::where("type", "twitch")->orderBy('user_id','desc')
                    ->chunk(100, function($users) {
            foreach ($users as $user) 
            {
            	try {
	            	Log::info("update profile for user_id: " . $user->user_id);
	            	$view_numb = Video::where("user_id", $user->user_id)->sum("view_numb");
	            	$user->view_numb = $view_numb;
	            	$options = [
			            'limit' => 0,
			        ];
                    $userMain = User::find($user->user_id);
                    if($userMain != null)
                    {
                        $twitchUser = TwitchApi::user($user->social_id);
                        $userMain->avatar = $twitchUser["logo"];
                        $userMain->save();
                    }
                    
	            	$twitchFollowers = TwitchApi::followers($user->social_id, $options);
	            	$twitchFollowings = TwitchApi::followings($user->social_id, $options);
	            	$user->follower_numb = $twitchFollowers["_total"];
	            	$user->following_numb = $twitchFollowings["_total"];
	            	try {
	            	TwitchApi::setToken($user->access_token);
	            	$twitchSub = TwitchApi::subscribers($user->social_id, $options);
		            $user->subscriber_numb = $twitchSub["_total"];
	            	}
	            	catch(\Exception $e)
	            	{
	            		Log::info("get user profile form twitch error subscriber");
	            		//Log::info($e);
	            	}
	            	$user->save();
            	}
            	catch(\Exception $e)
            	{
            		Log::info("get user profile form twitch error");
            		Log::info($e);
            	}
            }
        });
    }
    public static function updateProfileMixer()
    {
        $accounts = SocialAccount::where("type", "mixer")->orderBy('user_id','desc')
                    ->chunk(100, function($users) {
            foreach ($users as $user) 
            {
                try {
                    Log::info("update profile for user_id: " . $user->user_id);
                    $view_numb = Video::where("user_id", $user->user_id)->sum("view_numb");
                    $user->view_numb = $view_numb;
                   
                    $mixerUser = MixerHelper::getCurrentUser($user);
                    $userMain = User::find($user->user_id);
                    if($userMain != null && $mixerUser != null)
                    {
                        $userMain->avatar = $mixerUser["avatarUrl"];
                        $userMain->save();
                        $user->follower_numb = $mixerUser["channel"]["numFollowers"];
                        $user->subscriber_numb = $mixerUser["channel"]["numSubscribers"];
                         $user->following_numb = $mixerUser["followings"];
                    }
                    $user->save();
                }
                catch(\Exception $e)
                {
                    Log::info("get user profile form mixer error");
                    Log::info($e);
                }
            }
        });
    }
    public static function updateProfileYoutube()
    {
        $accounts = SocialAccount::where("type", "youtube")->orderBy('user_id','desc')
                    ->chunk(100, function($users) {
            foreach ($users as $user) 
            {
                try {
                    Log::info("update profile for user_id: " . $user->user_id);

                    $view_numb = Video::where("user_id", $user->user_id)->sum("view_numb");
                    $user->view_numb = $view_numb;

                    $youtubeUser = Youtube::getUserChannelById($user);

                    $userMain = User::find($user->user_id);

                    if($userMain != null && $youtubeUser != null)
                    {
                        //$userMain->avatar = $youtubeUser["avatarUrl"];
                        //$userMain->save();

                        $user->follower_numb = $youtubeUser->statistics["subscriberCount"];
                        $user->subscriber_numb = $youtubeUser->statistics["subscriberCount"];
                        $user->following_numb = $youtubeUser->statistics["subscriberCount"];
                    }

                    $user->save();
                }
                catch(\Exception $e)
                {
                    Log::info("get user profile form mixer error");
                    Log::info($e);
                }
            }
        });
    }
    public static function updateAccessToken($social, $data)
    {
        if(isset($data["access_token"]) && $social != null)
        {
            $social->access_token = $data["access_token"];
			if(isset($data["token_type"]) && $data["token_type"] != null)
				$social->token_type = $data["token_type"];
            $social->expires = $data["expires_in"];
            $social->expire_in = Carbon::now()->addSeconds($data["expires_in"]);
			if(isset($data["refresh_token"]) && $data["refresh_token"] != null)
				$social->refresh_token = $data["refresh_token"];
            $social->save();
            User::flushLoginInfo($social->user_id);
        }
        return $social;
    }
}