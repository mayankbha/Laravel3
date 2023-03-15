<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Redis;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Models\VideoGame;
use App\Models\Game;
use Lang;

class CreateCached extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-cached {cmd=all} {game=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cache for boom website';

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
        $cmd = $this->argument('cmd');
        if ($cmd == 'all'){
            $this->high_light_cache();
            $this->created_recent_cached();
            $this->createVideoGamesCached();
        }
        else{
            if ($cmd == "recent"){
                $this->created_recent_cached();
            }
            elseif($cmd == "highlight"){
                $this->high_light_cache();
            }
            elseif($cmd == "game"){
                $game_id = $this->argument('game');
                $game = Game::where('id',$game_id)->first();
                $this->createVideoGameCached($game);
            }
        }

    }

    private function high_light_cache()
    {
        $high_light_key = Lang::get("cached.highlight");
        $high_light_map_key = $high_light_key . "." . "map";
        Redis::del($high_light_key);
        Redis::del($high_light_map_key);
        $number_filter_highlight = config("view.number_highlight_video");
        $limit = 100;
        $offset = 0;
        $i = 0;
        $last_user_array = array();
        while (true) {
            $all_video = Video::getHighLightVideo($offset,$limit);
            $offset = $offset + $limit;
            foreach ($all_video as $video) {
                if ($i == 0) {
                    Redis::zadd($high_light_key, $video->id, json_encode($video));
                    Redis::zadd($high_light_map_key, $video->user_id, json_encode($video));
                    $i++;
                    array_push($last_user_array,$video->user_id);
                } elseif ($i < $number_filter_highlight) {
                    if (!in_array($video->user_id,$last_user_array)) {
                        Redis::zadd($high_light_key, $video->id, json_encode($video));
                        Redis::zadd($high_light_map_key, $video->user_id, json_encode($video));
                        $i++;
                        array_push($last_user_array,$video->user_id);
                    }
                    else{
                        continue;
                    }
                } else {
                    break;
                }
            }
            if (count($all_video) < $limit) {
                break;
            }

            if ($i < $number_filter_highlight) {
                continue;
            } else {
                break;
            }
        }

        $output = new ConsoleOutput();
        $output->writeln("Ok update highlight cached.");


    }

    private function created_recent_cached()
    {
        $key_cached = Lang::get("cached.recent");
        $key_map_cached = $key_cached . ".map";
        Redis::del($key_cached);
        Redis::del($key_map_cached);
        $i = 0;
        $offset = 0;
        $limit = 100;
        $last_user_array = array();
        $number_filter_recent = config("view.number_recent_video");
        while (true) {
            $all_video = Video::where('status', 1)->orderBy('id', 'desc')->take($limit)->offset($offset)->get();
            $offset = $offset + $limit;
            foreach ($all_video as $video) {
                if ($i == 0) {
                    Redis::zadd($key_cached, $video->id, json_encode($video));
                    Redis::zadd($key_map_cached, $video->user_id, json_encode($video));
                    $i++;
                    array_push($last_user_array,$video->user_id);
                } elseif ($i < $number_filter_recent) {
                    if (!in_array($video->user_id,$last_user_array)) {
                        Redis::zadd($key_cached, $video->id, json_encode($video));
                        Redis::zadd($key_map_cached, $video->user_id, json_encode($video));
                        $i++;
                        array_push($last_user_array,$video->user_id);
                    }
                    else{
                        continue;
                    }
                } else {
                    break;
                }
            }
            if (count($all_video) < $limit) {
                break;
            }

            if ($i < $number_filter_recent) {
                continue;
            } else {
                break;
            }
        }
        $output = new ConsoleOutput();
        $output->writeln("Created recent cached");
    }

    public function createVideoGamesCached()
    {
        $list_active_game = Game::all();

        foreach ($list_active_game as $game) {
            $this->createVideoGameCached($game);
        }
    }

    public function createVideoGameCached($game){
        $number_game_video = config('view.number_game_video');
        $video_game_key_cached = Lang::get('cached.game', array('gamename' => $game->id));
        $video_game_key_cached_map = $video_game_key_cached . ".map";
        Redis::del($video_game_key_cached);
        Redis::del($video_game_key_cached_map);
        $list_videos = Video::getVideoByGameId($game->id, 0);
        $last_key = 0;
        $last_user_array = array();
        $last_user_id = 0;
        $count = 0;
        $i = 0;
        foreach ($list_videos as $key => $video) {
            if ($i == 0) {
                Redis::zadd($video_game_key_cached, $video->id, json_encode($video));
                Redis::zadd($video_game_key_cached_map, $video->user_id, json_encode($video));
                $i++;
                array_push($last_user_array,$video->user_id);
                $last_key = $key;
            } elseif ($i < $number_game_video) {
                if (!in_array($video->user_id,$last_user_array)) {
                    Redis::zadd($video_game_key_cached, $video->id, json_encode($video));
                    Redis::zadd($video_game_key_cached_map, $video->user_id, json_encode($video));
                    $i++;
                    array_push($last_user_array,$video->user_id);
                    $last_key = $key;
                }
                else{
                    continue;
                }
            } else {
                break;
            }
        }
        while (Redis::zcard($video_game_key_cached) < $number_game_video) {
            $last_key++;
            if (isset($list_videos[$last_key])) {
                Redis::zadd($video_game_key_cached, $list_videos[$last_key]->id, json_encode($list_videos[$last_key]));
            } else {
                break;
            }
        }
        $output = new ConsoleOutput();
        $output->writeln("Created videos of game $video_game_key_cached : done!");
    }
}
