<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Video;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Log;

class VideoEvent extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    public function created(Video $video){
        Log::info('create');
    }

    public function updated(Video $video){
        Log::   info('updated');
    }
}
