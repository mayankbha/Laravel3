<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use TwitchApi;
use Log;
use Carbon\Carbon;
use App\Models\ViewerUserSub;
use App\Models\Viewer;
use Redis;

class GetSubcriberByUser extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $index;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($index)
    {
        //
        $this->index = $index;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $list_streamer = Redis::get('list-user-sub');

        $list_streamer = json_decode($list_streamer, true);

        $item = isset($list_streamer[$this->index]) ? $list_streamer[$this->index] : array();

        if (count($item)) {
            $options = [
                'limit' => 100,
                'offset' => 0,
                'direction' => 'desc',
            ];
            $first = 1;
            while($options['offset'] < $item['subscriber_numb']){
                $state = 0;
                try {
                    TwitchApi::setToken($item['access_token']);
                    $twitchSub = TwitchApi::subscribers($item['social_id'], $options);
                    $item['subscriber_numb'] = $twitchSub['_total'];
                    foreach ($twitchSub['subscriptions'] as $row) {
                        unset($row['user']['logo']);
                        unset($row['user']['bio']);
                        $viewer = Viewer::createOrUpdate(['name' => $row['user']['name']]);
                        $viewer_user_sub_check = ViewerUserSub::where('viewer_id',$viewer->id)->where('user_id',$item['id'])->first();
                        if (!$viewer_user_sub_check){
                            $viewer_user_sub = new ViewerUserSub(['viewer_id' => $viewer->id,'user_id'=>$item['id']]);
                            $viewer_user_sub->save();
                        }
                        else{
                            $state = 1;
                            break;
                        }
                    }
                    Log::info("Job check user {$item['id']}");
                } catch (\Exception $e) {
                    Log::info("Job get user profile form twitch error subscriber");
                    //Log::info($e);
                    $state = 1;
                }
                $options['offset'] += $options['limit'];
                if ($state){
                    break;
                }
            }
            $start = $this->index + 1;
            dispatch((new GetSubcriberByUser($start))->onQueue('GetUserSub')->delay(10));
        } else {
            return true;
        }
    }
}
