<?php

namespace App\Console\Commands;

use App\Jobs\FollowListStreamerMixer;
use App\Jobs\FollowListStreamerYoutube;
use Illuminate\Console\Command;

use App\Jobs\FollowListStreamer;
use Redis;

class StartFollowListStreamer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:start-follow-list-streamer';

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
        //
        $redis = Redis::connection("boombot");
        $redis->del("queues:followListStreamer:reserved");
        $redis->del("queues:followListStreamer");

        $job = (new FollowListStreamer(0))->onQueue("followListStreamer");
        dispatch($job);

        $redis->del("queues:followListStreamerMixer:reserved");
        $redis->del("queues:followListStreamerMixer");

        $job = (new FollowListStreamerMixer(0))->onQueue("followListStreamerMixer");
        dispatch($job);

        $redis->del("queues:followListStreamerYoutube:reserved");
        $redis->del("queues:followListStreamerYoutube");

        $job = (new FollowListStreamerYoutube(0))->onQueue("followListStreamerYoutube");
        dispatch($job);
    }
}
