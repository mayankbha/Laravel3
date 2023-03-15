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

class FollowListStreamerMixer extends Job implements ShouldQueue
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
        $key_cache = Lang::get("cached.followListStreamerMixer");
        $min = $this->offset;
        $max = $this->offset + 24;
        Log::info("start follow list streamer mixer");
        $list_user = $redis->lrange($key_cache, $min, $max);
        $user_string = implode(";", $list_user);
        if (count($list_user)) {
            try {
                $client = new GuzzleHttp\Client();
                $headers = ['Accept' => 'application/vnd.twitchtv.v3+json',
                    'Client-ID' => config('twitch-api.client_id'),
                ];
                $res = $client->request('GET', 'https://mixer.com/api/v1/channels?where=token:in:' . $user_string, [
                    'headers' => $headers,
                ]);
                $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(), true);
                $check_user_array = [];
                foreach ($content_stream_status as $item) {
                    $key_user_cached = Lang::get("cached.streamerStateType", ['name' => $item['token'], 'type' => User::USER_TYPE_MIXER]);
                    $user_exist = $redis->get($key_user_cached);
                    $data = [];
                    if ($user_exist) {
                        $user_exist = json_decode($user_exist, true);
                        $data['followStatus'] = $user_exist['followStatus'];
                    } else {
                        $data['followStatus'] = 1;
                    }
                    $data['totalViewer'] = $item['viewersCurrent'];
                    $data['status'] = 1;
                    $data['jobCount'] = 1;
                    $redis->setex($key_user_cached, config("follow-streamer.cached.startTimeout"), json_encode($data));
                    $check_user_array[] = $item['token'];
                }
                Log::info("[FollowListStreamerMixer] success check {$user_string}");
                foreach ($list_user as $value) {
                    if (!in_array($value, $check_user_array)) {
                        $key_user_cached = Lang::get("cached.streamerStateType", ['name' => $value,'type'=>2]);
                        $user_exist = $redis->get($key_user_cached);
                        $data = [];
                        if ($user_exist) {
                            $user_exist = json_decode($user_exist, true);
                            $data['followStatus'] = $user_exist['followStatus'];
                            $data['totalViewer'] = isset($user_exist['totalViewer']) ? $user_exist['totalViewer'] : 0;
                        } else {
                            $data['followStatus'] = 1;
                            $data['totalViewer'] = 0;
                        }
                        $data['jobCount'] = 1;
                        $data['status'] = 0;
                        $redis->setex($key_user_cached, config("follow-streamer.cached.startTimeout"), json_encode($data));
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception->getTraceAsString());
            }
        }
        if ($this->offset + 25 > $redis->llen($key_cache)) {
            $this->offset = 0;
        } else {
            $this->offset = $this->offset + 25;
        }
        sleep(8);
        $job = (new FollowListStreamerMixer($this->offset))->onQueue("followListStreamerMixer");
        dispatch($job);
        return true;
    }
}
