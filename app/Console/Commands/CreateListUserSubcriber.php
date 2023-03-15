<?php

namespace App\Console\Commands;

use App\Jobs\GetSubcriberByUser;
use Illuminate\Console\Command;
use DB;
use TwitchApi;
use Log;
use Carbon\Carbon;
use App\Models\ViewerUserSub;
use App\Models\Viewer;
use Redis;

class CreateListUserSubcriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-list-user-sub {cmd=update}';

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
        $list_streamer = DB::select( DB::raw("SELECT u.name as name,sc.access_token,sc.social_id,u.id as id,sc.subscriber_numb as subscriber_numb FROM `users` as u,social_accounts as sc where u.id = sc.user_id and sc.subscriber_numb > 0 and is_streamer = 1 order by sc.subscriber_numb desc"));
        $list_streamer = json_decode(json_encode($list_streamer),true);

        $cmd = $this->argument('cmd');

        if ($cmd == "update"){
            Redis::set('list-user-sub',json_encode($list_streamer));
            $start = 0;
            dispatch((new GetSubcriberByUser($start))->onQueue('GetUserSub'));
        }
        elseif ($cmd == "create"){
            foreach($list_streamer as $item){
                $options = [
                    'limit' => 100,
                    'offset' => 0,
                    'direction' => 'desc',
                ];
                $first = 1;
                $array_insert = [];
                while ($options['offset'] < $item['subscriber_numb']){
                    try {

                        TwitchApi::setToken($item['access_token']);
                        $twitchSub = TwitchApi::subscribers($item['social_id'], $options);
                        $item['subscriber_numb'] = $twitchSub['_total'];
                        foreach ($twitchSub['subscriptions'] as $row) {
                            unset($row['user']['logo']);
                            unset($row['user']['bio']);
                            $viewer = Viewer::createOrUpdate(['name'=> $row['user']['name']]);
                            $array_insert[] = ['viewer_id' => $viewer->id,'user_id'=>$item['id'],'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()];
                        }
                        $count = count($array_insert);
                        Log::info("Get sub okie {$item['id']} {$count} {$item['subscriber_numb']}");
                    }
                    catch(\Exception $e)
                    {
                        Log::info("get user profile form twitch error subscriber {$item['id']}");
                        Log::info($e);
                    }
                    $options['offset'] += $options['limit'];
                }
                ViewerUserSub::insert($array_insert);
            }
        }

    }
}
