<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Log;
use Redis;
use App\Models\User;
use Lang;

class CreateUserGameList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-user-game-list {cmd=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user game list';

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
        $cmd = $this->argument("cmd");
        if ($cmd == "all") {
            $this->createAllUserGameList();
        } else {
            $user = User::where('id', $cmd)->orWhere('name', $cmd)->first();
            if (!$user) {
                print_r("user not exist");
                return flase;
            }
            $this->createUserGameList($user);
        }
    }

    private function createAllUserGameList()
    {
        $all_user = User::all();
        foreach ($all_user as $item) {
            $this->createUserGameList($item);
        }
        print_r("Ok");
    }

    private function createUserGameList($user)
    {
        $query = "select g.game_id as game_id,count(g.id) as user_game_count 
                  from videos as v,video_game as g 
                  where v.id = g.video_id and v.status = 1 and v.user_id = {$user->id} 
                  GROUP by g.game_id 
                  ORDER by user_game_count DESC
                 ";

        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        $data = new Collection($results);
        $data = $data->keyBy("game_id");
        $data->sortByDesc("user_game_count");
        $key_cached = Lang::get('cached.userGameList',['user_id'=>$user->id]);
        Redis::set($key_cached,json_encode($data));
    }
}
