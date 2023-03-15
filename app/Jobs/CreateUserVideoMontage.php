<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\SessionStreamer;
use App\Models\SocialAccount;
use App\Models\Video;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Models\LiveStream;
use DB;

class CreateUserVideoMontage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $session;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session)
    {
        //
        $this->session = $session;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $current_session = SessionStreamer::where('id',$this->session->id)->first();

        if ($current_session->stoptime != $this->session->stoptime){
            Video::dispatchCreateUserVideoMontageJob($current_session);
            Log::info("Stop time updated, delay job: {$this->session->user_id} {$this->session->id}");
            return true;
        }
        $checked_time = Carbon::instance(new \DateTime($this->session->stoptime))->addSeconds(config('live-stream.time_between_session'));
        $current_live_stream = LiveStream::where('user_id',$this->session->user_id)->where('is_live',1)->where('started_time','<=',$checked_time)->first();
        if ($current_live_stream){
            $this->session->stoptime = Carbon::now();
            $this->session->save();
            Video::dispatchCreateUserVideoMontageJob($this->session);
            Log::info("User is live streaming, update stoptime ,delay job: {$this->session->user_id} {$this->session->id}");
            return true;
        }

        $same_live_stream = LiveStream::where('user_id',$this->session->user_id)->where(DB::raw('date_add(started_time, interval -30 minute)'),'<=',$this->session->starttime)->where(DB::raw('date_add(stopped_time, interval 30 minute)'),">=",$this->session->stoptime)->first();
        if ($same_live_stream){
            Log::info("[createUserVideoMontage]Have a LiveStream with duration of session, replace starttime {$same_live_stream->started_time} and replace stoptime {$same_live_stream->stopped_time}");
            if ($this->session->starttime > $same_live_stream->started_time){
                $this->session->starttime = $same_live_stream->started_time;
            }
            if ($this->session->stoptime < $same_live_stream->stopped_time){
                $this->session->stoptime = $same_live_stream->stopped_time;
            }
        }


        $start_time = Carbon::instance(new \DateTime($this->session->starttime))->timestamp;
        $stop_time = Carbon::instance(new \DateTime($this->session->stoptime))->timestamp;
        if ($stop_time - config('live-stream.min_duration') < $start_time){
            Log::info("Duration of session too short: {$this->session->user_id} {$this->session->id}");
            return true;
        }

        $user = User::where('id',$this->session->user_id)->first();
        if (!$user){
            Log::info("User is not exist {$this->session->user_id}");
            return true;
        }
        $social_account = SocialAccount::where('user_id',$user->id)->first();
        if (!$social_account){
            Log::info("SocialAccount is not exist {$user->id}");
            return true;
        }
        /*$list_allow_create_montage = config('live-stream.list_allow_create_montage');
        if ($social_account->subscriber_numb < 1 && !in_array($user->name,$list_allow_create_montage)){
            Log::info("$user->name is not in list of users who allow create montage. Subscriber numb < 1");
            return true;
        }*/
        $start_time = Carbon::instance(new \DateTime($this->session->starttime));
        $stop_time = Carbon::instance(new \DateTime($this->session->stoptime));
        $list_videos = Video::where('user_id',$this->session->user_id)->where('status',1)->where('created_at','>=',$start_time)->where('created_at','<=',$stop_time)->get();

        if(count($list_videos) < config('live-stream.min_number_videos_montage')){
            Log::info("Not enough videos: {$this->session->user_id} {$this->session->id}");
             // has previous
            Video::createMontageForSession($this->session->id, true);
            //return true;
        }
        Log::info("Ready create montage {$start_time} => {$stop_time} : {$this->session->user_id}");
        Video::createMontageForSession($this->session->id);
        Log::info("End job create montage");

    }
}
