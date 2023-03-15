<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\SocialAccount;
use GuzzleHttp;
use Lang;
use Redis;
use Log;
use Carbon\Carbon;
use App\Helpers\YoutubeHelper;

class FollowStreamer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = $this->user;
        $redis = Redis::connection("boombot");

        if ($user->type == User::USER_TYPE_TWITCH){
            $key_cached = Lang::get("cached.streamerState",['name'=>$user->name]);
        }
        elseif ($user->type == User::USER_TYPE_MIXER){
            $key_cached = Lang::get("cached.streamerStateType",['name'=>$user->name,'type'=>$user->type]);
        }
        elseif ($user->type == User::USER_TYPE_YOUTUBE){
            $key_cached = Lang::get("cached.streamerStateType",['name'=>$user->name,'type'=>$user->type]);
        }
        $user_exist = $redis->get($key_cached);
        if (!$user_exist){
            Log::info("[FollowStreamer] stop follow {$user->name}");
            return true;
        }
        $user_exist = json_decode($user_exist,true);

        $last_follow_rq_key = Lang::get('cached.lastFollowRequest',['server_ip'=>gethostname(),'type'=>$user->type]);
        $last_follow_request_time = $redis->get($last_follow_rq_key);

        if ($last_follow_request_time){
            if (Carbon::now()->timestamp < $last_follow_request_time + 2){
                if ($user->type == User::USER_TYPE_TWITCH){
                    User::dispatchFollowStreamerJob($user,1);
                    return true;
                }
                elseif ($user->type == User::USER_TYPE_MIXER){
                    User::dispatchFollowMixerStreamerJob($user,1);
                    return true;
                }
                elseif ($user->type == User::USER_TYPE_YOUTUBE){
                    User::dispatchFollowYoutubeStreamerJob($user,1);
                    return true;
                }
            }
        }
        $redis->set($last_follow_rq_key,Carbon::now()->timestamp);

        $data = [];
        $data['followStatus'] = $user_exist['followStatus'];
        $data['totalViewer'] = $user_exist['totalViewer'];
        $data['status'] = $user_exist['status'];

        $streamer_offline_count_key = Lang::get('cached.streamerOfflineCount',['name'=>$user->name,'type'=>$user->type]);
        $streamer_offline_count = $redis->get($streamer_offline_count_key);
        if (is_null($streamer_offline_count)){
            $streamer_offline_count = 0;
        }

        try{
            if ($this->user->type == User::USER_TYPE_TWITCH){
                $client = new GuzzleHttp\Client();
                $headers = ['Accept' => 'application/vnd.twitchtv.v3+json',
                    'Client-ID' => config('twitch-api.client_id'),
                ];
                $res = $client->request('GET', 'https://api.twitch.tv/kraken/streams/' . $user->name, [
                    'headers' => $headers,
                ]);
                $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
                if ($content_stream_status['stream'] == null){
                    if ($streamer_offline_count < 2){
                        $streamer_offline_count++;
                    }
                    else{
                        $streamer_offline_count = 0;
                        $data['status'] = 0;
                        $data['totalViewer'] = 0;
                    }
                }
                else{
                    $streamer_offline_count = 0;
                    $data['status'] = 1;
                    $data['totalViewer'] = $content_stream_status['stream']['viewers'];
                }
                Log::info("[FollowStreamer] Check stream state {$user->name} twitch");
            }
            elseif($this->user->type == User::USER_TYPE_MIXER){
                $client = new GuzzleHttp\Client();
                $headers = ['Accept' => 'application/json',
                ];
                $res = $client->request('GET', 'https://mixer.com/api/v1/channels/' . $user->name, [
                    'headers' => $headers,
                ]);
                $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
                if ($content_stream_status['online']) {
                    $streamer_offline_count = 0;
                    $data['status'] = 1;
                    $data['totalViewer'] = $content_stream_status['viewersCurrent'];
                }
                else{
                    if ($streamer_offline_count < 2){
                        $streamer_offline_count++;
                    }
                    else{
                        $streamer_offline_count = 0;
                        $data['status'] = 0;
                        $data['totalViewer'] = $content_stream_status['viewersCurrent'];
                    }
                }
                Log::info("[FollowStreamer] Check stream state {$user->name} mixer");
            }
            elseif($this->user->type == User::USER_TYPE_YOUTUBE){
                $youtube_is_live = YoutubeHelper::getChannelBroadcastStatus($user->name);

                if ($youtube_is_live) {
                    $streamer_offline_count = 0;
                    $data['status'] = 1;
                    $data['totalViewer'] = YoutubeHelper::getCurrentViewer($user->name);
                }
                else{
                    if ($streamer_offline_count < 2){
                        $streamer_offline_count++;
                    }
                    else{
                        $streamer_offline_count = 0;
                        $data['status'] = 0;
                        $data['totalViewer'] = 0;
                    }
                }
                Log::info("[FollowStreamer] Check stream state {$user->name} youtube");
            }

        }
        catch (\Exception $e){
            Log::error($e);
        }
        Log::info("[FollowStreamer] Success check streamer {$user->name}");
        $data['jobCount'] = 1;

        if ($user_exist['followStatus'] == 0){
            $data['jobCount'] = 0;
            $redis->setex($key_cached,config("follow-streamer.cached.stopTimeout"),json_encode($data));
            $redis->setex($streamer_offline_count_key,config("follow-streamer.cached.startTimeout"),0);
            Log::info("[FollowStreamer] stop follow {$user->name}");
            return true;
        }
        else{
            $key_list_all_streamer = Lang::get("cached.followListAllStreamer");
            $redis->sadd($key_list_all_streamer,$user->id);
        }
        $redis->setex($streamer_offline_count_key,config("follow-streamer.cached.startTimeout"),$streamer_offline_count);
        $redis->setex($key_cached,config("follow-streamer.cached.startTimeout"),json_encode($data));
        if ($user->type == User::USER_TYPE_TWITCH){
            $key_list_streamer_cache = Lang::get("cached.followListStreamer");
            $list_streamer = $redis->lrange($key_list_streamer_cache,0,-1);
            if (!in_array($user->name,$list_streamer)){
                $redis->lpush($key_list_streamer_cache,$user->name);
            }
        }
        elseif ($user->type == User::USER_TYPE_MIXER){
            $key_list_streamer_cache = Lang::get("cached.followListStreamerMixer");
            $list_streamer = $redis->lrange($key_list_streamer_cache,0,-1);
            if (!in_array($user->name,$list_streamer)){
                $redis->lpush($key_list_streamer_cache,$user->name);
            }
        }
        elseif ($user->type == User::USER_TYPE_YOUTUBE){
            $key_list_streamer_cache = Lang::get("cached.followListStreamerYoutube");
            $list_streamer = $redis->lrange($key_list_streamer_cache,0,-1);
            if (!in_array($user->name,$list_streamer)){
                $redis->lpush($key_list_streamer_cache,$user->name);
            }
        }
        return true;
    }
}
