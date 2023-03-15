<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Redis;
use App\Models\ViewerUserSub;
use App\Models\Viewer;
use App\Models\User;
use Carbon\Carbon;
use Log;

class AddSubscriberViewer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:add-subscriber-viewer';

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

        $list_user_sub = $redis->keys("*boombot*");

        foreach ($list_user_sub as $item){
            $item_array = explode(".",$item);
            if (count($item_array) != 3){
                continue;
            }
            try{
                $user_name = $item_array[1];
                $viewer_name = $item_array[2];

                $data = $redis->get($item);

                $data_array = explode(",",$data);
                $subscriber_month = isset($data_array[0]) ? $data_array[0] : 0;

                $user = User::where("name",$user_name)->first();
                $viewer = Viewer::createOrUpdate(['name'=>$viewer_name]);
                ViewerUserSub::insert(['viewer_id' => $viewer->id,'user_id'=>$user->id,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now(),'subscriber_month'=>$subscriber_month]);
            }
            catch (\Exception $exception){
                Log::info($exception->getTraceAsString());
            }

        }
    }
}
