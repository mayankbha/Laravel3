<?php

namespace App\Jobs;

use App\Helpers\AWSHelper;
use App\Helpers\Helper;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

use App\Models\Video;

class RemoveVideoOnS3 extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    protected $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        //
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $state = AWSHelper::deleteVideoOns3($this->video->id);
        if ($state['status']){
            Log::info("Delete video success:" . json_encode($state));
            $this->video->status = 4;
            $this->video->save();
        }
        else{
            $this->video->status = 5;
            $this->video->save();
            Log::error("Something error" . json_encode($state));
        }
        return $state['status'];
    }
}
