<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\LiveStream;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\ViewerStreamLog;
use App\Traits\LiveStreamTrackingJobTrait;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use League\Flysystem\Exception;
use Redis;
use Log;
use Lang;
use GuzzleHttp;
use App\Helpers\MixerHelper;
use App\Helpers\YoutubeHelper;

class LiveStreamTracking extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels , LiveStreamTrackingJobTrait;


    private $live_stream ;

    private $auto_merge;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LiveStream $liveStream,$auto_merge = false)
    {
        //
        $this->live_stream = $liveStream;
        $this->auto_merge = $auto_merge;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        // get live stream
        $live_stream = LiveStream::where('id',$this->live_stream->id)->first();
        // if stream off => stop job
        if ($live_stream->is_live == 0){
            return true;
        }
        
        // check last request api
        $last_tracking_rq_key = Lang::get('cached.lastTrackingRequest');
        $last_tracking_request_time = Redis::get($last_tracking_rq_key);

        if ($last_tracking_request_time){
            if (Carbon::now()->timestamp < $last_tracking_request_time + 5){
                $job = (new LiveStreamTracking($this->live_stream))->onQueue('LiveStreamTracking')->delay(3);
                dispatch($job);
                return true;
            }
        }
        Log::info("Start tracking {$this->live_stream->id}");

        $user = User::where('id',$this->live_stream->user_id)->first();

        if (!$user){
            return true;
        }
        if($user->type == User::USER_TYPE_MIXER)
        {
            $mixer = SocialAccount::where("user_id", $user->id)
                    ->where("type", "mixer")->first();
            if($mixer)
            {
                $channelInfo = MixerHelper::getChannelInfo($mixer->channel_id);
                if($channelInfo["online"])
                {
                    $viewersCurrent = $channelInfo["viewersCurrent"];
                    // get current viewer redis

                    $key_cached = Lang::get('cached.liveStreamTracking',array('username'=>$this->live_stream->id));
                    $viewersCurrentRedis = Redis::get($key_cached);
                    if($viewersCurrent > $viewersCurrentRedis || 
                        $viewersCurrentRedis == null)
                    {
                        Redis::setex($key_cached,24*3600,$viewersCurrent);
                    }
                    
                }
            }
        }
        if($user->type == User::USER_TYPE_YOUTUBE)
        {
            $youtube = SocialAccount::where("user_id", $user->id)
                ->where("type", "youtube")->first();
            if($mixer)
            {
                $youtube_is_live = YoutubeHelper::getChannelBroadcastStatus($youtube->channel_id);
                if($youtube_is_live)
                {
                    $viewersCurrent = YoutubeHelper::getCurrentViewer($youtube->channel_id);
                    // get current viewer redis

                    $key_cached = Lang::get('cached.liveStreamTracking',array('username'=>$this->live_stream->id));
                    $viewersCurrentRedis = Redis::get($key_cached);
                    if($viewersCurrent > $viewersCurrentRedis ||
                        $viewersCurrentRedis == null)
                    {
                        Redis::setex($key_cached,24*3600,$viewersCurrent);
                    }

                }
            }
        }

        if($user->type == User::USER_TYPE_TWITCH)
        {
            // Re tracking a live stream that had data for twitch
            if ($this->auto_merge){
                $viewers_log = ViewerStreamLog::where("live_stream_id",$this->live_stream->id)->first();
                if ($viewers_log){
                    $key_cached = Lang::get('cached.liveStreamTracking',array('username'=>$this->live_stream->id));
                    $old_viewers = explode(" ",$viewers_log->viewer);
                    if (count($old_viewers)){
                        Redis::sadd($key_cached,$old_viewers);
                    }
                }
            }
            // get viewer for twitch
            $tmi_url = "http://tmi.twitch.tv/group/user/{$user->name}/chatters";
            Redis::set($last_tracking_rq_key,Carbon::now()->timestamp);
            $content = @file_get_contents($tmi_url);

            $content_array = json_decode($content,true);

            $viewers = isset($content_array['chatters']['viewers']) ? $content_array['chatters']['viewers'] : [];

            if (!isset($content_array['chatters']['viewers'])){
                Log::info('Get tmi api error');
                Log::info($content);
                $job = (new LiveStreamTracking($this->live_stream))->onQueue('LiveStreamTracking')->delay(20);
                dispatch($job);
                return true;
            }

            $key_cached = Lang::get('cached.liveStreamTracking',array('username'=>$this->live_stream->id));
            if (count($viewers)){
                Redis::sadd($key_cached,$viewers);
            }

            if ($user->id == 58){
                $dir_path = storage_path('app/tmi-content/');
                if (!is_dir($dir_path)) {
                    mkdir($dir_path, 0755, true);
                }
                $date = str_slug(Carbon::now()->toDateTimeString());
                $file_path = $dir_path . "58-{$date}.txt";
                @file_put_contents($file_path,$content);
            }

            $social_account = SocialAccount::where('user_id',$user->id)->first();
            try{
                $client = new GuzzleHttp\Client();
                $headers = ['Accept' => 'application/vnd.twitchtv.v5+json',
                    'Client-ID' => config('twitch-api.client_id'),
                ];
                $res = $client->request('GET', 'https://api.twitch.tv/kraken/streams/' . $social_account->social_id, [
                    'headers' => $headers,
                ]);
                $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
                if ($content_stream_status['stream'] == null){
                    if ($live_stream->current_off_request_number + 1 >= config('live-stream.max_off_request')){
                        $live_stream->is_live = 0;
                        $live_stream->stopped_time = Carbon::now();
                        $live_stream->save();
                        $this->createLiveStreamStopJob($live_stream);
                    }
                    else{
                        $live_stream->current_off_request_number = $live_stream->current_off_request_number + 1;
                        $live_stream->save();
                    }
                }
                else{
                    $live_stream->current_off_request_number = 0;
                    $live_stream->save();
                }
                Log::info("Check stream state {$user->name}");
            }
            catch (Exception $e){
                Log::error($e);
            }
            // end get viewer for twitch
        }

        if ($live_stream->is_live){
            $job = (new LiveStreamTracking($live_stream))->onQueue('LiveStreamTracking')->delay(5*60);
            dispatch($job);
        }

        Log::info("End tracking {$this->live_stream->id}");


    }
}
