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

class FollowListStreamerYoutube extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $offset;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($offset)
    {
        //
        $this->offset = $offset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $redis = Redis::connection("boombot");
        $key_cache = Lang::get("cached.followListStreamerYoutube");
        $min = $this->offset;
        $max = $this->offset;
        Log::info("start follow list streamer youtube");
        $list_user = $redis->lrange($key_cache, $min, $max);
        if (count($list_user)) {
            try {
                $name = $list_user[0];
                $user = User::where('name',$name)->first();
                $data = [];
                $youtube_is_live = YoutubeHelper::getChannelBroadcastStatus($user->name);
                if ($youtube_is_live) {
                    $data['status'] = 1;
                    $data['totalViewer'] = YoutubeHelper::getCurrentViewer($user->name);
                }
                else{
                    $data['status'] = 0;
                    $data['totalViewer'] = 0;
                }
                $key_user_cached = Lang::get("cached.streamerStateType", ['name' => $user->name, 'type' => User::USER_TYPE_YOUTUBE]);
                $user_exist = $redis->get($key_user_cached);

                if ($user_exist) {
                    $user_exist = json_decode($user_exist, true);
                    $data['followStatus'] = $user_exist['followStatus'];
                } else {
                    $data['followStatus'] = 1;
                }
                $data['jobCount'] = 1;
                $redis->setex($key_user_cached, config("follow-streamer.cached.startTimeout"), json_encode($data));
                Log::info("FollowListStreamerYoutube " . json_encode($data));

            } catch (\Exception $exception) {
                Log::info($exception->getTraceAsString());
            }
        }
        if ($this->offset + 1 > $redis->llen($key_cache)) {
            $this->offset = 0;
        } else {
            $this->offset = $this->offset + 1;
        }
        sleep(8);
        $job = (new FollowListStreamerYoutube($this->offset))->onQueue("followListStreamerYoutube");
        dispatch($job);
        return true;
    }
}
