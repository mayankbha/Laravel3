<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Jobs\CachedUpdated;
use App\Models\VideoGame;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
Use App\Models\Video;
use Illuminate\Support\Facades\Redis;
use Log;
use Lang;

class ProcessVideoUpdate extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    protected $video;

    protected $action;

    protected $video_game;

    protected $dirty;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video, $action, $video_game = null, $dirty = array())
    {
        //
        $this->video = $video;
        $this->action = $action;
        $this->video_game = $video_game;
        $this->dirty = $dirty;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if ($this->action == "Created" || $this->action == "Updated") {
            $this->updateHighlightCached();
            $this->updateVideoGameCached();
            $this->updatedRecentVideo();
            $this->updateUserGameList();
        }
        if ($this->action == "VideoGameUpdated" || $this->action == "VideoGameCreated") {
            $this->updateVideoGameCached();
        }

        if ($this->action == "VideoGameCreated") {
            $this->createUserGameList();
        }

        if ($this->action == "VideoGameUpdating") {
            $this->updatingVideoGameCached();
        }

        Log::info('End update cached rule :  ' . $this->action . " - " . $this->video->code);

    }

    private function createUserGameList()
    {
        $key_cached = Lang::get('cached.userGameList', ['user_id' => $this->video->user_id]);
        $data = Redis::get($key_cached);
        if ($data) {
            $data = json_decode($data, true);
        } else {
            $data = array();
        }
        if (isset($data[$this->video_game->game_id])) {
            $data[$this->video_game->game_id]['user_game_count']++;
        } else {
            $data[$this->video_game->game_id]['user_game_count'] = 1;
        }
        Redis::set($key_cached, json_encode($data));
    }

    private function updateUserGameList()
    {
        $video_game = VideoGame::where('video_id', $this->video->id)->get();
        $key_cached = Lang::get('cached.userGameList', ['user_id' => $this->video->user_id]);
        $data = Redis::get($key_cached);
        if ($data) {
            $data = json_decode($data, true);
        } else {
            $data = array();
        }
        foreach ($video_game as $item) {
            if (isset($this->dirty['status'])) {
                if ($this->video->status != 1) {
                    if (isset($data[$item->game_id])) {
                        if ($data[$item->game_id]['user_game_count'] >= 2) {
                            $data[$item->game_id]['user_game_count']--;
                        } else {
                            unset($data[$item->game_id]);
                        }
                    }
                } else {
                    if (isset($data[$item->game_id])) {
                        $data[$item->game_id]['user_game_count']++;
                    } else {
                        $data[$item->game_id]['user_game_count'] = 1;
                    }
                }
            }
        }
        Redis::set($key_cached, json_encode($data));
    }

    private function updateHighlightCached()
    {
        $key_cached = Lang::get("cached.highlight");
        $key_cached_map = $key_cached . ".map";
        if ($this->video->status == 1) {
            $check_recent_exist = Redis::zrevrangebyscore($key_cached, $this->video->id, $this->video->id, array('limit' => array(0, 1)));
            if (count($check_recent_exist)) {
                Redis::zremrangebyscore($key_cached, $this->video->id, $this->video->id);
                Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
            } else {
                if ($this->video->type == Video::TYPE_MONTAGE){
                    $data_highlight_cache = Redis::zrevrangebyscore($key_cached, PHP_INT_MAX, 0, array('limit' => array(0, 1)));
                    if (count($data_highlight_cache) == 1) {
                        foreach ($data_highlight_cache as $key => $value) {
                            $data_highlight_cache[$key] = new Video(json_decode($value, true));
                        }
                        if ($this->video->id > $data_highlight_cache[0]->id) {
                            $current_user_video_array = Redis::zrevrangebyscore($key_cached_map, $this->video->user_id, $this->video->user_id, array('limit' => array(0, 1)));
                            if (count($current_user_video_array)) {
                                $current_user_video = new Video(json_decode($current_user_video_array[0], true));

                                Redis::zremrangebyscore($key_cached, $current_user_video->id, $current_user_video->id);
                                Redis::zremrangebyscore($key_cached_map, $current_user_video->user_id, $current_user_video->user_id);

                                Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
                                Redis::zadd($key_cached_map, $this->video->user_id, json_encode($this->video));
                            } else {
                                Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
                                Redis::zadd($key_cached_map, $this->video->user_id, json_encode($this->video));
                            }
                        }
                    } else {
                        Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
                        Redis::zadd($key_cached_map, $this->video->user_id, json_encode($this->video));
                    }
                }

            }
        } else {
            $check_highlight_exist = Redis::zrevrangebyscore($key_cached, $this->video->id, $this->video->id, array('limit' => array(0, 1)));
            if (count($check_highlight_exist)) {
                Redis::zremrangebyscore($key_cached, $this->video->id, $this->video->id);
                Redis::zremrangebyscore($key_cached_map, $this->video->user_id, $this->video->user_id);
            }

        }

        $number_highlight = config("view.number_highlight_video");
        if (Redis::zcard($key_cached) > $number_highlight) {
            while (true) {
                if (Redis::zcard($key_cached) > $number_highlight) {
                    $video = Redis::ZRANGE($key_cached, 0, 0);
                    if (count($video)) {
                        $temp = new Video(json_decode($video[0], true));
                        Redis::zremrangebyscore($key_cached_map, $temp->user_id, $temp->user_id);
                        Redis::zremrangebyrank($key_cached, 0, 0);
                    }
                } else {
                    break;
                }
            }
        }
    }

    private function updateVideoGameCached()
    {
        $video_game = VideoGame::where('video_id', $this->video->id)->get();
        foreach ($video_game as $item) {
            $video_game_key_cached = Lang::get("cached.game", array('gamename' => $item->game_id));
            $video_game_key_cached_map = $video_game_key_cached . ".map";
            if ($this->video->status == 1) {
                $check_video_game_exsit = Redis::zrevrangebyscore($video_game_key_cached, $this->video->id, $this->video->id, array('limit' => array(0, 1)));
                if (count($check_video_game_exsit)) {
                    Redis::zremrangebyscore($video_game_key_cached, $this->video->id, $this->video->id);
                    Redis::zadd($video_game_key_cached, $this->video->id, json_encode($this->video));
                } else {
                    $data_video_game = Redis::zrevrangebyscore($video_game_key_cached, PHP_INT_MAX, 0, array('limit' => array(0, 1)));
                    if (count($data_video_game) == 1) {
                        foreach ($data_video_game as $key => $value) {
                            $data_video_game[$key] = new Video(json_decode($value, true));
                        }
                        if ($this->video->id > $data_video_game[0]->id) {
                            $current_user_video_array = Redis::zrevrangebyscore($video_game_key_cached_map, $this->video->user_id, $this->video->user_id, array('limit' => array(0, 1)));
                            if (count($current_user_video_array)) {
                                $current_user_video = new Video(json_decode($current_user_video_array[0], true));

                                Redis::zremrangebyscore($video_game_key_cached, $current_user_video->id, $current_user_video->id);
                                Redis::zremrangebyscore($video_game_key_cached_map, $current_user_video->user_id, $current_user_video->user_id);

                                Redis::zadd($video_game_key_cached, $this->video->id, json_encode($this->video));
                                Redis::zadd($video_game_key_cached_map, $this->video->user_id, json_encode($this->video));
                            } else {
                                Redis::zadd($video_game_key_cached, $this->video->id, json_encode($this->video));
                                Redis::zadd($video_game_key_cached_map, $this->video->user_id, json_encode($this->video));
                            }
                        }
                    } else {
                        Redis::zadd($video_game_key_cached, $this->video->id, json_encode($this->video));
                        Redis::zadd($video_game_key_cached_map, $this->video->user_id, json_encode($this->video));
                    }

                }

            } else {
                $check_exist = Redis::zrevrangebyscore($video_game_key_cached, $this->video->id, $this->video->id, array('limit' => array(0, 1)));
                if (count($check_exist)) {
                    Redis::zremrangebyscore($video_game_key_cached, $this->video->id, $this->video->id);
                    Redis::zremrangebyscore($video_game_key_cached_map, $this->video->user_id, $this->video->user_id);

                }
            }
            $number_video_game = config("view.number_game_video");
            if (Redis::zcard($video_game_key_cached) > $number_video_game) {
                while (true) {
                    if (Redis::zcard($video_game_key_cached) > $number_video_game) {
                        Redis::zremrangebyrank($video_game_key_cached, 0, 0);
                    } else {
                        break;
                    }
                }
            }
        }
        $this->video->refreshCacheGameNames();

    }

    private function updatedRecentVideo()
    {
        $key_cached = Lang::get("cached.recent");
        $key_cached_map = $key_cached . ".map";
        if ($this->video->status == 1) {
            $check_recent_exist = Redis::zrevrangebyscore($key_cached, $this->video->id, $this->video->id, array('limit' => array(0, 1)));
            if (count($check_recent_exist)) {
                Redis::zremrangebyscore($key_cached, $this->video->id, $this->video->id);
                Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
            } else {
                $data_recent_cache = Redis::zrevrangebyscore($key_cached, PHP_INT_MAX, 0, array('limit' => array(0, 1)));
                if (count($data_recent_cache) == 1) {
                    foreach ($data_recent_cache as $key => $value) {
                        $data_recent_cache[$key] = new Video(json_decode($value, true));
                    }
                    if ($this->video->id > $data_recent_cache[0]->id) {
                        $current_user_video_array = Redis::zrevrangebyscore($key_cached_map, $this->video->user_id, $this->video->user_id, array('limit' => array(0, 1)));
                        if (count($current_user_video_array)) {
                            $current_user_video = new Video(json_decode($current_user_video_array[0], true));

                            Redis::zremrangebyscore($key_cached, $current_user_video->id, $current_user_video->id);
                            Redis::zremrangebyscore($key_cached_map, $current_user_video->user_id, $current_user_video->user_id);

                            Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
                            Redis::zadd($key_cached_map, $this->video->user_id, json_encode($this->video));
                        } else {
                            Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
                            Redis::zadd($key_cached_map, $this->video->user_id, json_encode($this->video));
                        }
                    }
                } else {
                    Redis::zadd($key_cached, $this->video->id, json_encode($this->video));
                    Redis::zadd($key_cached_map, $this->video->user_id, json_encode($this->video));
                }
            }
        } else {
            $check_recent_exist = Redis::zrevrangebyscore($key_cached, $this->video->id, $this->video->id, array('limit' => array(0, 1)));
            if (count($check_recent_exist)) {
                Redis::zremrangebyscore($key_cached, $this->video->id, $this->video->id);
                Redis::zremrangebyscore($key_cached_map, $this->video->user_id, $this->video->user_id);
            }

        }

        $number_recent = config("view.number_recent_video");
        if (Redis::zcard($key_cached) > $number_recent) {
            while (true) {
                if (Redis::zcard($key_cached) > $number_recent) {
                    $video = Redis::ZRANGE($key_cached, 0, 0);
                    if (count($video)) {
                        $temp = new Video(json_decode($video[0], true));
                        Redis::zremrangebyscore($key_cached_map, $temp->user_id, $temp->user_id);
                        Redis::zremrangebyrank($key_cached, 0, 0);
                    }
                } else {
                    break;
                }
            }
        }
    }

    private function updatingVideoGameCached()
    {

    }

    private function processListWhenDeleteItem($key_cached)
    {
        $before_list = Redis::zrangebyscore($key_cached, $this->video->id, PHP_INT_MAX, array('limit' => array(0, 2)));
        $after_list = Redis::zrevrangebyscore($key_cached, $this->video->id - 1, 0, array('limit' => array(0, 2)));
        if (count($before_list) == 0) {
            return;
        }
        if (count($after_list) == 0) {
            return;
        }
        $list_video = [];
        if (count($before_list) == 2) {
            if (count($after_list) == 2) {
                $list_video = [$before_list[1], $before_list[0], $after_list[0], $after_list[1]];
            } else {
                $list_video = [$before_list[1], $before_list[0], $after_list[0]];
            }
        } else {
            if (count($after_list) == 2) {
                $list_video = [$before_list[0], $after_list[0], $after_list[1]];
            } else {
                return;
                $list_video = [$before_list[0], $after_list[0]];
            }
        }

        $last_user_id = 0;
        $count = 0;
        foreach ($list_video as $item) {
            $video = new Video(json_decode($item, true));
            if ($last_user_id == 0) {
                $last_user_id = $video->user_id;
                $count = 1;
            } else {
                if ($count == 1) {
                    if ($last_user_id == $video->user_id) {
                        $count = 2;
                    } else {
                        $last_user_id = $video->user_id;
                        $count = 1;
                    }
                } else {
                    if ($last_user_id == $video->user_id) {
                        Redis::zremrangebyscore($key_cached, $video->id, $video->id);
                    } else {
                        $last_user_id = $video->user_id;
                        $count = 1;
                    }
                }
            }
        }
    }
}
