<?php

namespace App\Console\Commands;

use App\Models\LiveStream;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SocialAccount;
use GuzzleHttp;
use Log;

class ScanStreamerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:scan-streamer-status';

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
        $data_streamer = ['drdisrespectlive','ragefu','mym_alkapone','weak3n'];
        foreach ($data_streamer as $item){
            $user = User::where('name',$item)->first();
            $social_account = SocialAccount::where('user_id',$user->id)->first();
            $client = new GuzzleHttp\Client();
            $headers = ['Accept' => 'application/vnd.twitchtv.v5+json',
                'Client-ID' => config('twitch-api.client_id'),
            ];
            $res = $client->request('GET', 'https://api.twitch.tv/kraken/streams/' . $social_account->social_id, [
                'headers' => $headers,
            ]);
            $content_stream_status = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
            if ($content_stream_status['stream'] == null){
                Log::info("{$user->name} offline");
            }
            else{
                $live_stream_exist = LiveStream::where('user_id',$user->id)->first();
                if (!$live_stream_exist){
                    Log::info("{$user->name} online. Start live stream tracking.");
                    $body = ['username' => $user->name];
                    $res = $client->request('POST', 'https://boom.tv/api/streamerLiveStart', [
                        'form_params' => $body,
                    ]);
                    $content = GuzzleHttp\json_decode($res->getBody()->getContents(),true);
                }
            }
        }
    }
}
