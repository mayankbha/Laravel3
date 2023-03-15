<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\LiveStream;
use App\Models\User;
use App\Models\Viewer;
use App\Models\ViewerStreamLog;
use Carbon\Carbon;
use Doctrine\DBAL\Schema\View;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Redis;
use Log;
use Lang;

class LiveStreamStop extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $live_stream;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LiveStream $liveStream)
    {
        //
        $this->live_stream = $liveStream;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = User::where('id',$this->live_stream->user_id)->first();

        if (!$user){
            return true;
        }
        Log::info('start receive data');
        $key_cached = Lang::get('cached.liveStreamTracking',array('username'=>$this->live_stream->id));

        if($user->type == User::USER_TYPE_MIXER)
        {
            $viewerNumb = Redis::get($key_cached);
            $viewers_log = ViewerStreamLog::where('live_stream_id',$this->live_stream->id)->first();
            if ($viewers_log == null){
                $viewer_log = new ViewerStreamLog();
            }
            $viewer_log->viewer = "";
            $viewer_log->user_id = $user->id;
            $viewer_log->live_stream_id = $this->live_stream->id;
            $viewer_log->viewer_count = $viewerNumb;
            $viewer_log->save();
        }
        if($user->type == User::USER_TYPE_YOUTUBE)
        {
            $viewerNumb = Redis::get($key_cached);
            $viewers_log = ViewerStreamLog::where('live_stream_id',$this->live_stream->id)->first();
            if ($viewers_log == null){
                $viewer_log = new ViewerStreamLog();
            }
            $viewer_log->viewer = "";
            $viewer_log->user_id = $user->id;
            $viewer_log->live_stream_id = $this->live_stream->id;
            $viewer_log->viewer_count = $viewerNumb;
            $viewer_log->save();
        }
        if($user->type == User::USER_TYPE_TWITCH)
        {
            $viewers = Redis::smembers($key_cached);
            Redis::del($key_cached);
            if (count($viewers)){
                /*foreach ($viewers as $key=>$item){
                    $viewer = Viewer::createOrUpdate(['name'=>$item]);
                }*/
                $viewers_log_exist = ViewerStreamLog::where('live_stream_id',$this->live_stream->id)->first();
                if ($viewers_log_exist){
                    $viewers_log_exist->viewer = implode(" ",$viewers);
                    $viewers_log_exist->viewer_count = count($viewers);
                    $viewers_log_exist->save();
                }
                else{
                    $viewer_log = new ViewerStreamLog();
                    $viewer_log->user_id = $user->id;
                    $viewer_log->live_stream_id = $this->live_stream->id;
                    $viewer_log->viewer  = implode(" ",$viewers);
                    $viewer_log->viewer_count = count($viewers);
                    $viewer_log->save();
                }
            }
        }
        Log::info('End receive data ' . $key_cached);

    }
}
