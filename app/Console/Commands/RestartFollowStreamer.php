<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lang;
use Redis;
use App\Jobs\FollowStreamer;
use App\Models\User;
use GuzzleHttp;

class RestartFollowStreamer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:restart-follow-streamer';

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
        $client = new GuzzleHttp\Client();
        $headers = ['Accept' => 'application/json',
        ];
        $res = $client->request('GET', 'https://mixer.com/api/v1/channels?where=token:in:MoxArie;undeadhooligan;JJtheMachine;GL17CH;ThatPiGuy;Kells;RageReaper', [
            'headers' => $headers,
        ]);
        $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);

        print_r($content_stream_status);

    }
}
