<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\SocialAccount;
use App\Models\Video;
use App\Helpers\Helper;
use Log;
use Lang;
use Cache;
use App\Jobs\FollowStreamer;
use App\Models\SocialConnected;
use Carbon\Carbon;

class User extends Authenticatable
{
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const NORMAL_USER = 0;
    const SUPER_USER = 1;

    const SOURCE_WEB = 0;
    const SOURCE_APP = 1;
    const SOURCE_VR_APP = 2;

    const USER_TYPE_TWITCH = 0;
    const USER_TYPE_MIXER = 2;
    const USER_TYPE_YOUTUBE = 3;

    protected $fillable = [
        'id', 'uid', 'name', 'email', 'avatar', 'remember_token','account_type', 'displayname', 'type', 'is_claim', 'lasttime', 'source', 'is_streamer', 'allow_custom_meter', 'is_super', 'team_id'
    ];

    public function event()
    {
        return $this->hasMany('App\Models\BoomEvent');
    }

    public function subscription()
    {
        return $this->hasMany('App\Models\Subscription');
    }

    public function socialfollows()
    {
        return $this->hasMany('App\Models\SocialFollows');
    }

    public function videos()
    {
        return $this->hasMany('App\Models\Video');
    }
    public function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }
    public function socialaccount()
    {
        return $this->hasOne('App\Models\SocialAccount');
    }
    public function twitchoauth()
    {
        return $this->hasOne('App\Models\TwitchOauth');
    }
    public function images()
    {
        return $this->hasMany('App\Models\Image', "user_id");
    }

    public static function createOrUpdateAccount($account, $data)
    {
        $account_type = $data["account_type"];
        $isClaim = $data["is_claim"];
        $claimForUser = $data["claimForUser"];
        $token = $data["token"];
        $source = $data["source"];
        $isStreamer = $data["isStreamer"];
        $social = SocialAccount::where("social_id", $account["id"])->where('type',$account_type)->first();
        $user = null;
        $typeStreamBy = User::USER_TYPE_TWITCH;
        if($account_type == "mixer") $typeStreamBy = User::USER_TYPE_MIXER;
        if($account_type == "youtube") $typeStreamBy = User::USER_TYPE_YOUTUBE;
        
        if($social==null)
        {
            if($isClaim == 0 || (!$claimForUser && $isClaim))
            {
              $user= new User();
              $user->name = $account["name"];
              $user->displayname = $account["nickname"];
              $user->email = $account["email"];
              $user->avatar = $account["avatar"];
              $user->source = $source;
              $user->is_streamer = $isStreamer;
              $user->type = $typeStreamBy;
              $user->save();
              $user->code = Helper::generateCode($user->id);
              $user->save();
              $social= new SocialAccount();
              $social->user_id=$user->id;
              $social->social_id=$account["id"];
              $social->access_token=$account["token"];
              $social->token_type=$account["tokenType"];
              $social->channel_id=$account["channelId"];
              if($account_type=="twitter")
                $social->refresh_token=$account["tokenSecret"];
              else
              {
                $social->refresh_token=$account["refreshToken"];
                $social->expires=$account["expiresIn"];
                $social->expire_in = Carbon::now()->addSeconds($account["expiresIn"]);
              }
              $social->type=$account_type;
              $social->save();
              if($account_type=="twitch")
              {
                Log::info("start add maillist ");
                Helper::sendMailChimpRegister($user->email, $user->name);
              }
            }
        }
        else
        {
          $user=User::where('id',$social->user_id)->first();
          if (!$user){
              $user = new User();
          }
          $user->name = $account["name"];
          $user->displayname = $account["nickname"];
          $user->email = $account["email"];
          $user->avatar = $account["avatar"];
          if($source == User::SOURCE_APP && $user->is_streamer  == 0)
          {
            $user->is_streamer = 1;
          }
          $user->save();

          $social->access_token=$account["token"];
          if($account_type=="twitter")
            $social->refresh_token=$account["tokenSecret"];
          else
          {
            $social->refresh_token=$account["refreshToken"];
            $social->expires=$account["expiresIn"];
            $social->expire_in = Carbon::now()->addSeconds($account["expiresIn"]);
          }
          $social->save();
        }
        if($user != null)
        {
            if($account_type=="twitch" || $account_type=="mixer"  || $account_type=="youtube")
            {

                $new_token = new Token();
                $new_token->token = $token;
                $new_token->user_id = $user->id;
                $new_token->save();
            }
        }
        return $user;
    }

    public static function updateStreamer()
    {
      $users = User::all();
      foreach ($users as $key => $user) {
        $video = Video::where("user_id", $user->id)->count();
        if($video > 0 && $user->is_streamer == 0) 
        {
          Log::info("update is_streamer for user id: " . $user->id);
          $user->is_streamer = 1;
          $user->save();
        }
      }
    }
    public function getBoomMeterStatus()
    {
        $boomMeter = BoomMeter::where("user_code", $this->code)->first();
        $status = "default";
        if($boomMeter != null) 
        {
          $type = BoomMeterType::find($boomMeter->boom_meter_type_id);
          if($type != null)
          {
            $status = $type->name;
          }
        }
        return $status;
    }

    public static function apiGetLoginInfo($user_id){
        $results = array();
        $user=User::find($user_id);
        $userSocial=SocialAccount::where('user_id',$user_id)->first();
        $tokenUser = "";

        if($user->type == User::USER_TYPE_MIXER)
        {
            $boomtvUser = User::where('name','boomtvmod')
                     ->where("type", User::USER_TYPE_MIXER)->first();
        }
    else if($user->type == User::USER_TYPE_YOUTUBE)
        {

            if (config("app.env") == "boom-beta"){
                $boomtvUser = User::where('name','UCwyUedRsABDvGmi1m6J7qvw')
                    ->where("type", User::USER_TYPE_YOUTUBE)->first();
            }
            elseif(config("app.env") == "production"){
                $boomtvUser = User::where('name','UCwyUedRsABDvGmi1m6J7qvw')
                    ->where("type", User::USER_TYPE_YOUTUBE)->first();
            }
            else{
                $boomtvUser = User::where('name','UCwyUedRsABDvGmi1m6J7qvw')
                    ->where("type", User::USER_TYPE_YOUTUBE)->first();
            }
        }
        else
        {
            $boomtvUser=User::where('name','boomtvmod')
             ->where("type", User::USER_TYPE_TWITCH)->first();
        }
        
        if($boomtvUser)
        {
            $boomtvSocial = SocialAccount::where("user_id", $boomtvUser->id)->first();
            $autoQuality = config("video.auto_quality");
            /* twitter_streamer:
                auto_tweet, twitter_connected
            */
            $twitterConnectedObj = SocialConnected::where("user_id",$user_id)
                      ->where("type", "twitter")->first();
            $autoTweet = "0";
            $twitterConnected = "0";
            if($twitterConnectedObj != null)
            {
              $autoTweet = $twitterConnectedObj->auto_tweet;
              $twitterConnected = "1";
            }
            if($userSocial != null)
            {
              $tokenUser = $userSocial->access_token;
            }
            $results = array(  "status" => 0,
                    "access_token" => $boomtvSocial->access_token,
                    "nick" => $boomtvUser->name,
                    "channel"=>$user->name,
                    "twitch_token" => $tokenUser,
                    "boom_meter" => $user->code,
                    "auto_quality" => $autoQuality,
                    "replay_view_type" =>$user->replay_view_type,
                    "timezone" => $user->timezone,
                    "auto_tweet" => $autoTweet,
                    "twitter_connected" => $twitterConnected,
                    "mixer_access_token" => $boomtvSocial->access_token,
                    "mixer_token" => $tokenUser,
                    "account_type" => $boomtvSocial->type,
                    "token_type" => $boomtvSocial->token_type,
                    "youtube_channel_id" => $userSocial->channel_id,
                    "youtube_displayname" => $user->displayname
                    );
           if($user->type == User::USER_TYPE_MIXER)
            {
              $results["access_token"] = "";
              $results["twitch_token"] = "";
            }
            else
            {
              $results["mixer_access_token"] = "";
              $results["mixer_token"] = "";
            }
            $resContent = response()->json($results);
        }
        else{
            $resContent = response()->json(array("status" => 1, "error" => "user BOT do not exist"));
        }
        return $resContent;
    }

    public static function flushLoginInfo($user_id){
        $key_cached = Lang::get('cached.apiGetLoginInfo',['id'=>$user_id]);
        $res_content = static::apiGetLoginInfo($user_id);
        Cache::put($key_cached,$res_content,24*60);
        Log::info("End flush login info {$user_id}");
    }

    public static function dispatchFollowStreamerJob($user,$delay = 60){
        $job = (new FollowStreamer($user))->onQueue("followStreamer")->delay($delay);
        $job = dispatch($job);
        return $job;
    }

    public static function dispatchFollowMixerStreamerJob($user,$delay = 60){
        $job = (new FollowStreamer($user))->onQueue("followMixerStreamer")->delay($delay);
        $job = dispatch($job);
        return $job;
    }

    public static function dispatchFollowYoutubeStreamerJob($user,$delay = 60){
        $job = (new FollowStreamer($user))->onQueue("followYoutubeStreamer")->delay($delay);
        $job = dispatch($job);
        return $job;
    }

    public function getProfileLink()
    {
      $link = "https://www.twitch.tv/".$this->name;
      if($this->type == USer::USER_TYPE_MIXER)
      {
        $link = "https://mixer.com/".$this->name;
      }
      return $link;
    }
    public function countVideos()
    {
      return Video::where("user_id", $this->id)->count();
    }
}