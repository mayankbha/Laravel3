<?php
/**
 * Created by PhpStorm.
 * User: tannn
 * Date: 5/5/17
 * Time: 10:52 AM
 */
namespace App\Traits;
use App\Models\LiveStream;
use App\Jobs\LiveStreamTracking;
use App\Jobs\LiveStreamStop;

trait LiveStreamTrackingJobTrait {
    protected function createStreamTrackingJob(LiveStream $liveStream,$auto_merge = false){
        $job = (new LiveStreamTracking($liveStream,$auto_merge))->onQueue('LiveStreamTracking');
        dispatch($job);
    }

    protected function createLiveStreamStopJob(LiveStream $liveStream){
        $job = (new LiveStreamStop($liveStream))->onQueue('LiveStreamTracking');
        dispatch($job);
    }
}