<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use GuzzleHttp;
use App\Models\SessionStreamer;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Video;
use Log;
use App\Models\LiveStream;
use Lang;
use Redis;

class BuildListStreamersLive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:build-list-streamers-live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->startBotCurrentLive();

    }

    public function startBotCurrentLive(){

        $redis = Redis::connection("boombot");
        /*$client = new GuzzleHttp\Client();
        $headers = ['Accept' => 'application/json',
        ];
        $res = $client->request('GET', 'https://mixer.com/api/v1/channels/' . 'tanboomtv', [
            'headers' => $headers,
        ]);
        $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
        var_dump($content_stream_status);*/
        $offset = 0;
        $online_stream = [];
        $time = microtime(true);
        while (true){
            $user = User::select('name')->where('is_streamer',1)->take(25)->offset($offset)->get();
            $offset = $offset + 25;
            $user = $user->toArray();
            foreach ($user as $key=>$value){
                $user[$key] = $value['name'];
            }

            $user_string = implode(',',$user);
            $client = new GuzzleHttp\Client();
            $headers = ['Accept' => 'application/vnd.twitchtv.v3+json',
                'Client-ID' => config('twitch-api.client_id'),
            ];
            $res = $client->request('GET', 'https://api.twitch.tv/kraken/streams?channel='.$user_string, [
                'headers' => $headers,
            ]);
            $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
            $streamer_online_count = 0;
            if (isset($content_stream_status['streams'])){
                $key_cache = Lang::get("cached.followListStreamer");
                foreach ($content_stream_status['streams'] as $value){
                    $redis->lpush($key_cache,$value['channel']['name']);
                }
            }
            if (count($user) < 25){
                break;
            }
            Log::info("stream count {$streamer_online_count}, ". $user_string);
        }
    }

    public function reCreateMontage(){
        $start_time = Carbon::instance(new \DateTime("2017-05-31 19:00:00"));
        $end_time = Carbon::instance(new \DateTime("2017-06-01 3:00:00"));
        $list_session = SessionStreamer::where('stoptime','>=',$start_time)->where('stoptime','<=',$end_time)->get();
        $list_substract = array();
        foreach ($list_session as $session){
            if ($session->user_id == 58){
                continue;
            }
            $item_subtrack = array();
            $user = User::where('id',$session->user_id)->first();
            if (!$user){
                Log::info("[ReCreateMontageJob] User is not exist {$session->user_id}");
                continue;
            }
            $social_account = SocialAccount::where('user_id',$user->id)->first();
            if (!$social_account){
                Log::info("[ReCreateMontageJob] SocialAccount is not exist {$user->id}");
                continue;
            }
            $list_allow_create_montage = config('live-stream.list_allow_create_montage');
            if ($social_account->subscriber_numb < 50 && !in_array($user->name,$list_allow_create_montage)){
                Log::info("[ReCreateMontageJob] $user->name is not in list of users who allow create montage. Subscriber numb < 50");
                continue;
            }
            $item_subtrack['name'] = $user->name;
            $item_subtrack['subscriber_numb'] = $social_account->subscriber_numb;
            $item_subtrack['session_start_time'] = $session->starttime;
            $item_subtrack['session_stop_time'] = $session->stoptime;
            $start_time = Carbon::instance(new \DateTime($session->starttime))->timestamp;
            $stop_time = Carbon::instance(new \DateTime($session->stoptime))->timestamp;
            if ($stop_time - config('live-stream.min_duration') < $start_time){
                Log::info("[ReCreateMontageJob] Duration of session too short: {$session->user_id}");
                $item_subtrack['note'] = "Duration of session too short {$session->user_id}";
                $list_substract[] = $item_subtrack;
                continue;
            }

            $start_time = Carbon::instance(new \DateTime($session->starttime));
            $stop_time = Carbon::instance(new \DateTime($session->stoptime));
            $list_videos = Video::where('user_id',$session->user_id)->where('status',1)->where('created_at','>=',$start_time)->where('created_at','<=',$stop_time)->get();

            if(count($list_videos) < config('live-stream.min_number_videos_montage')){
                Log::info("[ReCreateMontageJob] Not enough videos: {$session->user_id} $session->id");
                $item_subtrack['note'] = "Not enough videos: {$session->user_id} {$session->id}";
                $list_substract[] = $item_subtrack;
                continue;
            }
            $item_subtrack['note'] = "Ok: {$session->user_id} {$session->id}";
            Video::dispatchCreateUserVideoMontageJobWithoutDelay($session);
            $list_substract[] = $item_subtrack;
        }
        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $file_path = $dir_path . "old-session-check-{$start_time->toDateString()}.csv";
        $file = fopen($file_path, 'w');
        $first = 1;
        foreach ($list_substract as $row) {
            if ($first) {
                $array_key = [];
                foreach ($row as $key => $value) {
                    $array_key[] = $key;
                }
                fputcsv($file, $array_key);
                $first = 0;
            }
            fputcsv($file, $row);
        }
        fclose($file);
    }
}
