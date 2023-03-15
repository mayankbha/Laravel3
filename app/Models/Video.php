<?php

namespace App\Models;

use Doctrine\Common\Cache\Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use App\Helpers\FFmpegHelper;
use App\Models\VideoGame;
use Session;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact;
use App\Models\Token;
use App\Models\SessionStreamer;
use Log;
use Carbon\Carbon;
use DB;
use Auth;
use File;
use Redis;
use Illuminate\Support\Facades\Event;
use Lang;
use App\Jobs\CreateUserVideoMontage;
use App\Models\LiveStream;
use Illuminate\Support\Facades\Cache as Cached;

class Video extends Model
{

    protected $table = 'videos';
    const STATUS_ACTIVE = 1;
    // filter on browse
    const FILTER_NONE = 0;
    const FILTER_CAROUSEL = 1;
    const FILTER_TRENDING = 2;
    const FILTER_RECENT = 3;
    const FILTER_HIGHLIGHTS = 4;
    const FILTER_VIDEO360 = 5;
    const FILTER_GAME = 6;
    const FILTER_USER = 7;
    // Video type
    const TYPE_2D = 0;
    const TYPE_3D = 1;
    const TYPE_360 = 2;
    const TYPE_MONTAGE = 3;

    const SENT_MAIL_MONTAGE_NORMAL = 1;
    const SENT_MAIL_MONTAGE_TOP = 2;

    public static function boot()
    {

        parent::boot();

        static::created(function ($video) {
            Event::fire('Video.created', $video);
        });

        static::updated(function ($video) {
            $dirty = $video->getDirty();
            Event::fire('Video.updated', array($video, $dirty));
        });

        static::deleted(function ($video) {
            Event::fire('Video.deleted', $video);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'title', 'name', 'view_numb', 'link', 'links3', 'code', 'thumbnail', 'created_at', 'updated_at', 'rateavg', 'game_id', 'type', 'datetime', 'share_numb', 'job_id', 'link_hls', 'hls_type', 'like_numb', 'filter_type', 'view_week',
        'like_super', 'status', 'requested_by', 'super_like_time', 'session_id', 'sent_mail_montage'];


    public function video_game()
    {
        return $this->hasMany('App\Models\VideoGame', "video_id");
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function ratings()
    {
        return $this->hasMany('App\Models\Rating');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }

    public function getCreated()
    {
        return Helper::getTimeAgo($this->created_at);
    }

    public function getUserName()
    {
        $user = User::find($this->user_id);
        return $user->name;
    }

    public function getGameName()
    {
        $game = Game::find($this->game_id);
        return $game->name;
    }

    public function getAvgRating()
    {
        return $this->ratings()->avg('numb_star');
    }

    public function getPercent($numbStar)
    {
        $total = $this->ratings()->count();
        $star = $this->ratings()->where("numb_star", $numbStar)->count();
        $percent = 0;
        if ($total != 0) {
            $percent = $star * 100 / $total;
        }
        return $percent;
    }

    public function countVote()
    {
        return $this->ratings()->count();
    }

    public static function markFilterType()
    {
        $start_time = microtime(true);

        Log::info("start mark filter videos ");
        /*$users = Video::groupBy("user_id")->pluck("user_id");
        //get 15 video from other user top view_week
        $video_suggest = new Collection();
        foreach ($users as $id) {
            $video_suggest->add(Video::where("user_id", $id)->where("updated_at", ">=", Carbon::now()->subWeek())
                ->orderby('view_week', 'desc')->first());
        }

        $video_suggest = $video_suggest->sortByDesc("view_week");
        $number_carousel = config("view.carousel_limit");
        $number_limit_filter_carousel = config("view.limit_filter_carousel");
        $video_suggest = $video_suggest->slice(0,$number_limit_filter_carousel);*/


        $number_carousel = config("view.carousel_limit");
        $number_limit_filter_carousel = config("view.limit_filter_carousel");
        $video_suggest_array = DB::select('select t.id as user_id, v.id as id, v.view_week from videos v join 
                                          (SELECT u.id as id, MAX(v.view_week ) AS vc FROM users u INNER JOIN videos v ON u.id = v.user_id WHERE v.type != '.static::TYPE_MONTAGE.' and v.status = 1 GROUP BY u.id ORDER BY vc DESC) as t 
                                          where v.user_id = t.id and v.view_week = t.vc
                                          group by user_id, v.view_week order by v.view_week DESC limit ? offset ?',
            [$number_limit_filter_carousel, 0]);
        /*foreach ($video_suggest_array as $item){
            $video_suggest->add(Video::where("id",$item->vid)->get()->first());
        }*/
        $video_suggest = new Collection($video_suggest_array);
        $video_suggest = $video_suggest->keyBy("id");


        $video_suggest = $video_suggest->sortByDesc("view_week");
        $video_collection_top_10 = new Collection();
        $video_collection_top_30 = new Collection();
        $video_trending = new Collection();
        $video_carousel = new Collection();
        $i = 0;
        foreach ($video_suggest as $video) {
            if ($i < 10) {
                $video_collection_top_10->add($video);
            } elseif ($i >= 10 && $i < 30) {
                $video_collection_top_30->add($video);
            }
            $i++;
        }

        $video_carousel_1 = $video_collection_top_10->random(3)->toArray();
        $video_carousel_2 = $video_collection_top_30->random(3)->toArray();

        $video_carousel = new Collection(array_merge($video_carousel_1,$video_carousel_2));
        $video_carousel = $video_carousel->keyBy("id");
        foreach ($video_suggest as $video){
            if (!$video_carousel->get($video->id)){
                $video_trending->add($video);
            }
        }
        $video_trending = $video_trending->keyBy("id");

        $videoCarouselSuper = Video::where("like_super", ">=", 2)
            ->where("super_like_time", ">=", Carbon::now()->subWeek())->get();
        foreach ($videoCarouselSuper as $video) {
            $carousel_exists = $video_carousel->where("user_id", $video->user_id)->first();

            if ($carousel_exists) {
                if ($carousel_exists->id != $video->id) {
                    $video_trending->add($carousel_exists);
                    $video_carousel = $video_carousel->forget($carousel_exists->id);
                    $video_carousel->add($video);
                }
            } else {
                $video_trending->add($video_carousel->last());
                $video_carousel = $video_carousel->forget($video_carousel->last()->id);
                $video_carousel->add($video);
            }
            $video_carousel->keyBy("id");
        }
        $markOlds = Video::where("filter_type", Video::FILTER_CAROUSEL)
            ->orWhere("filter_type", Video::FILTER_TRENDING)
            ->update(['filter_type' => Video::FILTER_NONE]);
        foreach ($video_carousel as $item) {
            Video::where("id", $item->id)->first()->update(["filter_type" => Video::FILTER_CAROUSEL]);
        }
        foreach ($video_trending as $item) {
            Video::where("id", $item->id)->first()->update(["filter_type" => Video::FILTER_TRENDING]);
        }

        //end get carousel by view_week
        Log::info("end mark filter videos ");

        $ex_time = microtime(true) - $start_time;
        Log::info("markFilterType time execute : " . $ex_time);
    }


    public static function updateViewWeek()
    {
        $start_time = microtime(true);

        /*$videos = Video::where("updated_at", ">=", Carbon::now()->subWeek())
            ->chunk(100, function ($videos) {
                Log::info("--updateViewWeek--count : " . count($videos));
                foreach ($videos as $video) {
                    $viewWeek = ViewDay::where("video_id", $video->id)->where("created_at", ">=", Carbon::now()->subDays(2))->sum("view_numb");
                    Video::where("id", $video->id)->update(["view_week" => $viewWeek]);
                }
            });*/
        $sub_week = Carbon::now()->subWeek();
        $sub_2_day = Carbon::now()->subDays(1)->format('Y-m-d 00:00:00');
        DB::statement("update videos set view_week = (SELECT COALESCE(SUM(view_numb),0) FROM view_day WHERE video_id = videos.id AND created_at >= '{$sub_2_day}') where status = 1 and updated_at >= '{$sub_week}'");
        $ex_time = microtime(true) - $start_time;

        Log::info("updateViewWeek time execute : " . $ex_time);
    }

    public static function mark_filter_recent()
    {
        $start_time = microtime(true);
        $video_recent = new Collection();
        $i = 0;
        $offset = 0;
        $limit = 100;
        $user_id = 0;
        $last_user_id = 0;
        $count = 0;
        $number_filter_recent = config("view.number_filter_recent");
        while (true) {
            $videos = Video::where('status', 1)->orderBy('created_at', 'desc')->take($limit)->offset($offset)->get();
            $all_video = $videos;
            $all_video = $all_video->keyBy("id");
            $offset = $offset + $limit;
            foreach ($all_video as $video) {
                if ($i == 0) {
                    $video_recent->add($video);
                    $i++;
                    $last_user_id = $video->user_id;
                    $count = 1;
                } elseif ($i < $number_filter_recent) {
                    if ($count == 1) {
                        $video_recent->add($video);
                        $i++;
                        if ($video->user_id == $last_user_id) {
                            $count++;
                            $last_user_id = $video->user_id;
                        } else {
                            $count = 1;
                            $last_user_id = $video->user_id;
                        }
                    } elseif ($count == 2) {
                        if ($video->user_id == $last_user_id) {
                            continue;
                        } else {
                            $video_recent->add($video);
                            $i++;
                            $count = 1;
                            $last_user_id = $video->user_id;
                        }
                    }
                } else {
                    break;
                }
            }
            if (count($videos) < $limit) {
                break;
            }

            if ($i < $number_filter_recent) {
                continue;
            } else {
                break;
            }
        }
        $old_recent = Video::where('filter_recent', 1)->get();
        foreach ($old_recent as $key => $item) {
            $item->filter_recent = 0;
            $item->save();
        }
        foreach ($video_recent as $_key => $_item) {
            Video::where('id', $_item->id)->update(['filter_recent' => 1]);
        }

        $ex_time = microtime(true) - $start_time;
        Log::info("MarkFilterRecent execute time about : " . $ex_time);
    }

    public static function first_update_view_week()
    {
        $videos = Video::where('status', 1)->where("created_at", ">=", Carbon::now()->subWeek(2))
            ->chunk(100, function ($videos) {
                foreach ($videos as $video) {
                    $viewWeek = ViewDay::where("video_id", $video->id)->where("created_at", ">=", Carbon::yesterday())->sum("view_numb");
                    if ($viewWeek == 0) {
                        $view_day = new ViewDay();
                        $view_day->video_id = $video->id;
                        $view_day->view_numb = $video->view_numb;
                        $view_day->created_at = Carbon::yesterday();
                        $view_day->updated_at = Carbon::yesterday();
                        $view_day->date = Carbon::yesterday()->format('Y-m-d');
                        $view_day->save();
                        $viewWeek = $video->view_numb;
                    }
                    Video::where("id", $video->id)->update(["view_week" => $viewWeek]);
                }
            });
    }

    public static function getHighLightVideo($offset, $limit)
    {
        $query = Video::query();
        $query->where("status", 1);
        $query->where("videos.type", Video::TYPE_MONTAGE);
        $query->orderby("videos.created_at", "desc");
        $list_videos = $query->with('user')->take($limit)
            ->offset($offset)->get();
        return $list_videos;
    }

    public static function redisGetHighLightVideo($last_id, $limit)
    {
        $start_time = microtime(true);
        if ($last_id == 0) {
            $last_id = PHP_INT_MAX;
        }
        $key_cached = Lang::get('cached.highlight');
        $data = Redis::zrevrangebyscore($key_cached, $last_id - 1, 0, array('limit' => array(0, $limit)));
        $return_data = new Collection();
        foreach ($data as $item) {
            $video = new Video(json_decode($item, true));
            $return_data->add($video);
        }
        Log::info("Get highlight {$last_id} {$limit} in: " . (microtime(true) - $start_time));
        return $return_data;
    }

    public static function getLastHighlight($last_id)
    {
        $query = Video::query();
        $query->where("status", 1);
        $query->where("videos.type", Video::TYPE_MONTAGE);
        if ($last_id) {
            $query->where("id", ">", $last_id);
        }
        $query->orderby("videos.created_at", "desc");
        $list_videos = $query->with('user')->take(1)->offset(0)->get();

        return $list_videos;
    }

    public static function getVideoByGameId($gameid, $last_id)
    {
        $query = Video::query();
        $query->where("status", 1);
        $query->select(DB::raw("videos.*"));
        $query->join('video_game', function ($join) use ($gameid) {
            $join->on('videos.id', '=', 'video_game.video_id')
                ->where('video_game.game_id', "=", $gameid);
        });
        if ($last_id) {
            $query->where("videos.id", "<", $last_id);
        }
        $query->orderby("videos.created_at", "desc");
        $list_videos = $query->with('user')->get();
        return $list_videos;
    }

    public static function redisGetVideoByGameId($gameid, $last_id, $limit)
    {
        $start_time = microtime(true);
        if ($last_id == 0) {
            $last_id = PHP_INT_MAX;
        }
        $key_cached = Lang::get('cached.game', array('gamename' => $gameid));
        $data = Redis::zrevrangebyscore($key_cached, $last_id - 1, 0, array('limit' => array(0, $limit)));
        $return_data = new Collection();
        foreach ($data as $item) {
            $video = new Video(json_decode($item, true));
            $return_data->add($video);
        }
        Log::info("Get video by gameid {$gameid} {$last_id} {$limit} in: " . (microtime(true) - $start_time));
        return $return_data;
    }

    public static function fiterBy($filterBy, $gameId = 0, $page = 0, $pagination = 12, $last_id = 0)
    {
        $query = Video::query();
        $query->where("status", 1);
        if ($filterBy == Video::FILTER_CAROUSEL) {
            $query->where("filter_type", Video::FILTER_CAROUSEL);
            //$query->where("updated_at", ">=", Carbon::now()->subWeek());
            $query->orderby("view_week", "desc");
        }
        if ($filterBy == Video::FILTER_TRENDING) {
            $query->where("filter_type", Video::FILTER_TRENDING);
            $query->where("updated_at", ">=", Carbon::now()->subWeek());
            $query->orderby("view_week", "desc");
        }
        if ($filterBy == Video::FILTER_HIGHLIGHTS) {
            return Video::redisGetHighLightVideo($last_id, $pagination);
        }
        if ($filterBy == Video::FILTER_VIDEO360) {
            $query->where("videos.type", Video::TYPE_360);
            $query->orderby("videos.created_at", "desc");
        }
        if ($filterBy == Video::FILTER_GAME && is_numeric($gameId)
            && $gameId > 0
        ) {
            return static::redisGetVideoByGameId($gameId, $last_id, $pagination);
            /*$query->select(DB::raw("videos.*"));
            $query->join('video_game', function ($join) use ($gameId) {
                $join->on('videos.id', '=', 'video_game.video_id')
                    ->where('video_game.game_id', "=", $gameId);
            });
            $query->orderby("videos.created_at", "desc");*/
        }
        if ($filterBy == Video::FILTER_RECENT) {
            return static::redisGetRecentVideo($last_id, $pagination);
            /*$number_filter_recent = config("view.number_filter_recent");
            if ($page <= $number_filter_recent - 6) {
                $query->where("filter_recent", 1)->orderby("videos.created_at", "desc");
            }
            if ($page > $number_filter_recent - 6 && $page <= $number_filter_recent) {
                $page = 0;
                $query->where("filter_recent", 0)->orderby("videos.created_at", "desc");
            }
            if ($page > $number_filter_recent) {
                $page = $page - $number_filter_recent;
                $query->where("filter_recent", 0)->orderby("videos.created_at", "desc");
            }*/


        }
        $carouselLimit = config("view.carousel_limit");
        if ($filterBy == Video::FILTER_CAROUSEL) {
            $pagination = $carouselLimit;
            $page = 0;
        }
        $videos = $query->with('user')->take($pagination)
            ->offset($page)->get();
        return $videos;
    }

    public static function redisGetRecentVideo($last_id, $limit)
    {
        $start_time = microtime(true);
        if ($last_id == 0) {
            $last_id = PHP_INT_MAX;
        }
        $key_cached = Lang::get('cached.recent');
        $data = Redis::zrevrangebyscore($key_cached, $last_id - 1, 0, array('limit' => array(0, $limit)));
        $return_data = new Collection();
        foreach ($data as $item) {
            $video = new Video(json_decode($item, true));
            $return_data->add($video);
        }
        Log::info("Get recent {$last_id} {$limit} in: " . (microtime(true) - $start_time));
        return $return_data;
    }

    public static function filter_video_by_userid($filterBy, $user_id, $gameId = 0, $page = 0, $pagination = 10)
    {
        $query = Video::query();
        $query->where("status", 1);
        $query->where("user_id", $user_id);
        if ($filterBy == Video::FILTER_CAROUSEL) {
            $query->where("filter_type", Video::FILTER_CAROUSEL);
            $query->orderby("view_week", "desc");
        }
        if ($filterBy == Video::FILTER_TRENDING) {
            $query->where("filter_type", Video::FILTER_TRENDING);
            $query->orderby("view_week", "desc");
        }
        if ($filterBy == Video::FILTER_HIGHLIGHTS) {
            $query->where("videos.type", Video::TYPE_MONTAGE);
            $query->orderby("videos.created_at", "desc");
        }
        if ($filterBy == Video::FILTER_VIDEO360) {
            $query->where("videos.type", Video::TYPE_360);
            $query->orderby("videos.created_at", "desc");
        }
        if ($filterBy == Video::FILTER_GAME && is_numeric($gameId)
            && $gameId > 0
        ) {
            $query->select(DB::raw("videos.*"));
            $query->join('video_game', function ($join) use ($gameId) {
                $join->on('videos.id', '=', 'video_game.video_id')
                    ->where('video_game.game_id', "=", $gameId);
            });
            $query->orderby("videos.created_at", "desc");
        }
        if ($filterBy == Video::FILTER_RECENT) {
            $query->orderby("videos.created_at", "desc");
        }
        $carouselLimit = config("view.carousel_limit");
        if ($filterBy == Video::FILTER_CAROUSEL) {
            $pagination = $carouselLimit;
            $page = 0;
        }
        if ($filterBy == Video::FILTER_USER) {
            $query->orderby("videos.created_at", "desc");
        }
        $videos = $query->with('user')->take($pagination)
            ->offset($page)->get();
        return $videos;
    }

    public static function my_video($page = 0, $pagination = 10)
    {
        $query = Video::query();
        $user_id = Auth::id();
        if (!$user_id) {
            return array();
        }
        $query->where("status", 1);
        $query->where("user_id", $user_id);
        $query->orderby("videos.created_at", "desc");
        $videos = $query->with('user')->take($pagination)
            ->offset($page)->get();
        return $videos;
    }

    public static function getVideoByCondition($conditions, $pagination)
    {
        $page = 0;
        $videoUser = "";
        $gameId = "";
        $sortby = 0;
        if (isset($conditions["page"])) $page = $conditions["page"];
        if (isset($conditions["user_id"])) $videoUser = $conditions["user_id"];
        if (isset($conditions["game_id"])) $gameId = $conditions["game_id"];
        if (isset($conditions["sortby"])) $sortby = $conditions["sortby"];

        $query = Video::query();
        $query->where("status", Video::STATUS_ACTIVE);
        if ($page == "" || !is_numeric($page)) {

            $page = 0;
        }

        if ($videoUser != "" && is_numeric($videoUser)) {
            $query->where("user_id", $videoUser);
        }
        if ($gameId != "" && is_numeric($gameId)) {
            /*$query->where("game_id", $gameId);*/
            $vgames = VideoGame::where("game_id", $gameId)->get();
            $ids = [];
            foreach ($vgames as $key => $value) {
                array_push($ids, $value->video_id);
            }
            if (count($ids) > 0) {
                $query->whereIn("id", $ids);
            }
        }
        if ($sortby > 0 && $sortby <= 4) {
            if ($sortby == 1) {
                $query->orderby("created_at", "desc");
            }
            if ($sortby == 2) {
                $query->orderby("created_at", "asc");
            }
            if ($sortby == 3) {
                $query->orderby("title", "asc");
            }
            if ($sortby == 4) {
                $query->where("type", 2);
                $query->orderby("created_at", "desc");
            }
        } else {
            $query->orderby("view_numb", "desc");
            $query->orderby("like_numb", "desc");
        }
        $total = $query->with('user')->count();
        $videos = $query->with('user')->take($pagination)->offset($page - 1)->get();
        return ["videos" => $videos, "total" => $total];
    }

    public function getTitle()
    {
        $max_char = 38;
        $title = [];
        $temp = "";
        $real_title = preg_split('/\s+/', $this->title);
        $max_key = count($real_title) - 1;
        foreach ($real_title as $key => $value) {


            if (strlen($value) <= $max_char) {
                $new_str = $temp . " " . $value;

                if (strlen($new_str) > $max_char) {
                    $title[] = $temp;
                    $temp = $value;

                } else {
                    $temp = $new_str;
                    if ($key == $max_key)
                        $title[] = $temp;
                }
            } else {

                $title[] = substr($value, 0, $max_char - 3) . "...";
                break;

            }
            if (count($title) == 1) {
                if (strlen($title[0]) > $max_char)
                    $title[0] .= "...";
                if ($key < $max_key)
                    $title[0] .= "...";
                break;
            }


        }

        return $title;
    }

    public function formatTime($date = 1)
    {
        date_default_timezone_set('GMT');
        $gmt_datetime = new \DateTime($this->datetime);
        $current = new \DateTime();
        $yearCurrent = $current->format('Y');
        $yearCreateVideo = $gmt_datetime->format('Y');
        $formatYMD = "d M Y";
        if($yearCurrent == $yearCreateVideo)
        {
            $formatYMD = "M d";
        }
        $user_zone = request()->cookie('user_zone');
        if (!$user_zone)
            $user_zone = "+0";
        $timezone = new \DateTimeZone(config('timezone.' . $user_zone, '+0'));
        $user_datetime = $gmt_datetime->setTimeZone($timezone);
        if($date == 1) 
            return date_format($gmt_datetime, $formatYMD);
        elseif($date == 0) 
            return date_format($gmt_datetime, 'g:i A');
        else 
            return date_format($gmt_datetime, $formatYMD) . '  ' . date_format($gmt_datetime, 'g:i A');
    }


    public function getGameNames()
    {
        $key_cached = Lang::get('cached.gameNames',['id'=>$this->id]);
        $data = Cached::get($key_cached);
        if ($data){
            return $data;
        }
        $games = VideoGame::where("video_id", $this->id)->get();
        $gamesOfVideo = [];
        foreach ($games as $key => $value) {
            $gamesOfVideo[] = $value->game->name;
        }
        $data = implode(", ", $gamesOfVideo);
        Cached::put($key_cached,$data,7*24*60);
        return $data;
    }

    public function refreshCacheGameNames(){
        $key_cached = Lang::get('cached.gameNames',['id'=>$this->id]);
        $games = VideoGame::where("video_id", $this->id)->get();
        $gamesOfVideo = [];
        foreach ($games as $key => $value) {
            $gamesOfVideo[] = $value->game->name;
        }
        Log::info('[refreshCacheGameNames] call from job');
        $data = implode(", ", $gamesOfVideo);
        return Cached::put($key_cached,$data,7*24*60);
    }

    // send mail to user after upload video montage success
    public static function sendMailForVideoMontage($videoId)
    {
        if ($videoId != "") {
            $video = Video::find($videoId);
            $user = User::find($video->user_id);
            if ($video != null && $video->type == 3 && $video->sent_mail_montage == null) {
                if ($video->session_id != null && $video->session_id != "") {
                    $session = SessionStreamer::find($video->session_id);
                    $sessionTime = "";
                    $number_boom = 0;
                    if ($session == null) {
                        $number_boom = 2999;
                    } else {
                        $number_boom = $session->number_boom;
                        $user = User::find($session->user_id);
                        $sessionTime = $session->starttime;
                    }
                    $number_boom = number_format($number_boom);
                    $result = static::sendMailForVideoMontageByUser($user, $number_boom, $video, $sessionTime);
                    if($result)
                    {
                        $video->sent_mail_montage = Video::SENT_MAIL_MONTAGE_TOP;
                        $video->save();
                    }
                    
                } else {

                    if ($user != null && $user->email != null && $user->email != "") {
                        Log::info("get data to send mail for username: " . $user->name . " video id: " . $videoId);
                        $emailFrom = config("mail.sendMailForVideoMontage.sender");
                        $subject = config("mail.sendMailForVideoMontage.subject");
                        $sendername = config("mail.sendMailForVideoMontage.sendername");
                        $emailTo = $user->email;
                        $info = array();
                        $info["temp"] = "emails.send_to_user_montage";
                        $info["videoLink"] = $video->link;
                        $info["sender"] = $sendername;
                        Helper::sendMail($emailFrom, $emailTo, $subject, $info);
                        $video->sent_mail_montage = Video::SENT_MAIL_MONTAGE_NORMAL;
                        $video->save();
                    }
                }
            }
        }
    }

    public static function sendMailForVideoMontageByUser($user, $number_boom, $video, $sessionTime)
    {
        $emailFrom = config("mail.sendMailForVideoMomentMontage.sender");
        $current_day = Video::getDateMontage($sessionTime, "M d");
        $subject = "Top replays from your {$current_day} live stream";
        $sendername = config("mail.sendMailForVideoMomentMontage.sendername");
        $emailCC = config("mail.list_send_moment_montage");
        $env = config("video.createMontageFor");
        if($env != "real")
        {
            $emailCC = config("mail.list_send_moment_montage_beta");
        }
        /*$emailTo = "xoan.nt@boom.tv";
        $emailCC =  array("xoan.nt@anlab.info");*/
        $info = array();
        /*dateSubject, imageProfile, username, date, numberBoom,
        thumbnail, link, unsubscribe*/
        $info['dateSubject'] = $current_day;
        $info['imageProfile'] = $user->avatar;
        $info['username'] = $user->displayname;
        $info['numberBoom'] = $number_boom;
        /*$info['user'] = $user;
        $info['video'] = $video;
        $info["sender"] = $sendername;
        $info['user'] = $user;*/
        $info['date'] = Video::getDateMontage($sessionTime, "M d, Y");
        $info['link'] = route('playvideo',['v'=>$video->code]);
        $info['link_share'] = route('playvideo',['v'=>$video->code])."&ref=share";
        $info['thumbnail'] = config('aws.sourceLink').$video->thumbnail;
        $info['unsubscribe'] = UnsubscriberEmail::getUnsubscriberEmailLink($user->code);
        Log::info("data send mail : " . implode("---", $info));
        //Helper::sendMailCc($emailFrom, $emailTo,$emailCC, $subject, $info);
        $tempId = config("mail.template_id_moment_monatge");
        $recipients = config("mail.send_moment_montage_recipient");
        //$cc = config("mail.list_send_moment_montage");
        if (count($recipients) <= 0 && $user->email != "" && $user->email != null) {
            $recipients =   [
                                [
                                    'address' => [
                                        'name' => $user->displayname,
                                        'email' => $user->email,
                                    ],
                                ],
                            ];
        }
        $result = Helper::sendMailBySparkPostTemplate($tempId, $info, $recipients, $emailCC);
        return $result;
    }

    public static function sendMailForVideoMontageTest()
    {
        $user = User::where('id', 1907)->first();
        $session = SessionStreamer::where('id', 11664)->first();
        $video = Video::where('id', 28479)->first();
        static::sendMailForVideoMontageByUser($user, 1111, $video);
    }

    public function getTextShare()
    {
        return "Watch: " . $this->user->name . "'s highlight from " . $this->getGameNames() . ". " . $this->link;
    }

    public static function updateView($vcode, $viewNumb,
                                      $isSetView = false)
    {
        $video_info = Video::where('code', '=', $vcode)->first();
        $returnView = null;
        if ($video_info != null) {
            $v = $viewNumb;
            if (!$isSetView) {
                $v = $video_info->view_numb + $viewNumb;
            }
            $viewWeek = ViewDay::updateByDay($video_info->id, $viewNumb, $isSetView);
            $video_info->view_numb = $v;
            $video_info->view_week = $viewWeek;
            $video_info->save();
            $returnView = $v;
        }
        return Helper::sortSize($returnView);
    }

    public static function updateViewById($id, $viewNumb, $isSetView = false)
    {
        $video_info = Video::where('id', $id)->first();
        $returnView = null;
        $v = $viewNumb;
        if (!$isSetView) {
            $v = $video_info->view_numb + $viewNumb;
        }
        $viewWeek = ViewDay::updateByDay($video_info->id, $viewNumb, $isSetView);
        $video_info->view_numb = $v;
        $video_info->view_week = $viewWeek;
        $video_info->save();
        $returnView = $v;
        return Helper::sortSize($returnView);
    }

    public function getViewNumbSort()
    {
        return Helper::sortSize($this->view_numb);
    }

    public static function updateDurationForTrending()
    {
        $path = storage_path('trending');
        if (!is_dir($path)) {
            mkdir($path);
        }
        $filepath = storage_path() . "/trending/videos.json";
        $videos = Video::getTrendingCarousel();
        if (is_array($videos)) {
            $sourcelink = config("aws.sourceLink");
            foreach ($videos as $key => $video) {
                $command = "ffprobe -v quiet -of csv=p=0 -show_entries format=duration " . '"' . $sourcelink . str_replace(' ', '%20', $video["links3"]) . '"';
                $duration = shell_exec($command);
                $videos[$key]["duration"] = $duration;
            }
            $videoJson = json_encode($videos);
            $put = File::put($filepath, $videoJson);
        }
    }

    public static function getTrendingCarousel()
    {
        $return_data = [];
        $carousel_video = Video::fiterBy(Video::FILTER_CAROUSEL, 0, 0, 10);
        foreach ($carousel_video as $video) {
            $temp_video = [];
            $temp_video['image'] = config('aws.sourceLink') . $video->thumbnail;
            $temp_video['streamer_name'] = $video->user()->first()->displayname;
            if ($video->hls_type == 3) {
                if ($video->type == 2) {
                    $temp_video['hls_link'] = url("/hls360/") . $video->link_hls;
                } else {
                    $temp_video['hls_link'] = config('aws.sourceLink') . $video->link_hls;
                }
            } else {
                $temp_video['hls_link'] = "";
            }

            if ($video->type == 2) {
                $temp_video['links3'] = "/videos-360/2048" . $video->links3;
            } else {
                $temp_video['links3'] = $video->links3;
            }

            $temp_video['source_link'] = config('aws.sourceLink') . $temp_video['links3'];
            $temp_video['like_numb'] = $video->like_numb;
            $temp_video['view_numb'] = $video->view_numb;
            $temp_video['game_name'] = $video->getGameNames();

            $return_data[] = $temp_video;
        }

        $trending_video = Video::fiterBy(Video::FILTER_TRENDING, 0, 0, 20);
        foreach ($trending_video as $video) {
            $temp_video = [];
            $temp_video['image'] = config('aws.sourceLink') . $video->thumbnail;
            $temp_video['streamer_name'] = $video->user()->first()->displayname;
            if ($video->hls_type == 3) {
                if ($video->type == 2) {
                    $temp_video['hls_link'] = url("/hls360/") . $video->link_hls;
                } else {
                    $temp_video['hls_link'] = config('aws.sourceLink') . $video->link_hls;
                }
            } else {
                $temp_video['hls_link'] = "";
            }

            if ($video->type == 2) {
                $temp_video['links3'] = "/videos-360/2048" . $video->links3;
            } else {
                $temp_video['links3'] = $video->links3;
            }

            $temp_video['source_link'] = config('aws.sourceLink') . $temp_video['links3'];
            $temp_video['like_numb'] = $video->like_numb;
            $temp_video['view_numb'] = $video->view_numb;
            $temp_video['game_name'] = $video->getGameNames();
            $return_data[] = $temp_video;
        }
        $status = 1;
        $error = "Success";
        return $return_data;
    }

    public static function getUsers()
    {
        $dt = Carbon::now();
        $limitTime = config("video.limit_time");
        $userHasSessionDay = SessionStreamer::select(DB::raw('session.user_id, users.lasttime, session.starttime , session.stoptime'))
            ->join('users', 'users.id', '=', 'session.user_id')
            ->where("session.starttime", ">", $dt->subHours($limitTime))
            ->whereRaw('session.starttime > users.lasttime')
            ->get();
        return $userHasSessionDay;
    }

    public static function getVideosByUser($userId, $starttime, $stoptime)
    {
        $top_montage_numb = config("video.top_montage_numb");
        $data = array();
        // get token to upload
        $token = Token::select("token")->where("user_id", $userId)
            ->orderby("created_at", "desc")->first();
        if ($token != null) {
            $data["token"] = $token->token;
        }


        //get videos
        $data["videos"] = array();
        $videos = Video::where("user_id", $userId)
            ->where("created_montage", 0)
            ->where("status", Video::STATUS_ACTIVE)
            ->where('created_at', ">=", $starttime)
            ->where('created_at', "<=", $stoptime)
            ->orderby("like_numb", "desc")
            ->orderby("view_numb", "desc")
            ->limit($top_montage_numb)
            ->get();
        $videos = $videos->reverse();

        // get list games
        $data["listGame"] = Video::getListGame($userId, $starttime, $stoptime);
        $videosSum = Video::select(DB::raw('MAX(like_numb) as maxLikes,
                        MAX(view_numb) as maxViews'))
                       ->where("user_id", $userId)
                       ->where("created_montage", 0)
                       ->where("status", Video::STATUS_ACTIVE)
                       ->where('created_at', ">=", $starttime)
                       ->where('created_at', "<=", $stoptime)
                       ->orderby("like_numb", "desc")
                       ->limit($top_montage_numb)
                       ->get()->toArray();
        $data["maxLikes"] = $videosSum[0]["maxLikes"];
        $data["maxViews"] = $videosSum[0]["maxViews"];
        $data["videos"] = $videos;
        return $data;
    }

    public static function getListGame($userId, $starttime, $stoptime)
    {
        $top_montage_numb = config("video.top_montage_numb");
        $names = Video::join('video_game', 'videos.id', '=', 'video_game.video_id')
            ->join('games', 'games.id', '=', 'video_game.game_id')
            ->where('videos.created_at', ">=", $starttime)
            ->where('videos.created_at', "<=", $stoptime)
            ->where("videos.user_id", $userId)
            ->where("videos.status", Video::STATUS_ACTIVE)
            ->orderby("videos.like_numb", "desc")
            ->limit($top_montage_numb)
            ->groupBy("games.name")
            ->pluck("games.name");
        if (count($names) > 0) {
            return implode(",", $names->toArray());
        }
        return "Unknown";
    }

    public static function getTotalLikeBySession($session){
        $total_like = Video::select(DB::raw('sum(like_numb) as total_like'))
            ->where("user_id", $session->user_id)
            ->where("status", Video::STATUS_ACTIVE)
            ->where('created_at', ">=", $session->starttime)
            ->where('created_at', "<=", $session->stoptime)
            ->get()->toArray();
        if (isset($total_like[0])){
            $video_number_boom = $total_like[0]['total_like'];
        }
        else{
            $video_number_boom = 0;
        }
        if ($video_number_boom > $session->number_boom){
            $session_save = SessionStreamer::where('id',$session->id)->first();
            $session_save->number_boom = $video_number_boom;
            $session_save->save();
            Log::info("[Util] replaced current number boom = total like of list videos in session {$session->id}: $video_number_boom");
        }
        else{
            Log::info("[Util] used current number boom {$session->id}: $video_number_boom - {$session->number_boom}");
        }
        return $session->number_boom;
    }

    public static function getTimePreviousMontage($session)
    {
        $start = "";
        $stop = "";
        // get sessions previous, PDT timezone
            if($session != null)
            {
                $userId = $session->user_id;
                $startTime = $session->starttime;
                //get date for PDT
                $date = Video::getDateMontage($startTime, 'Y-m-d', "PDT");

                $dateCarbon = Carbon::parse($date);
                //get start,end PDT
                $startDay = $dateCarbon->startOfDay(); 
                $start = Video::getDateMontage($startDay, 'Y-m-d H:i:s', "UTC","PDT");  
                /*$endDay = $dateCarbon->endOfDay();
                // convert start, stop PDT to UTC
                
                $stop = Video::getDateMontage($endDay, 'Y-m-d H:i:s', "UTC","PDT");*/

                Log::info("Start and end PDT to UTC : " . $start . " and " . $session->stoptime);
                return ["start" => $start, "stop" => $session->stoptime];
            }

            return null;
    }

    public static function createMontageForSession($sessionId, $hasPrevious = false)
    {
        $u = SessionStreamer::where("id",$sessionId)
                            ->where("status",SessionStreamer::CREATED_STATUS)
                            ->first();

        if ($u != null) 
        {
            $id = $u->user_id;
            $starttime = $u->starttime;
            $stoptime = $u->stoptime;
        }
        else return false;
        if($hasPrevious)
        {
            $session = SessionStreamer::find($sessionId);
            $id = $session->user_id;
            $time = Video::getTimePreviousMontage($session);
            if($time != null)
            {
                $starttime = $time["start"];
                $stoptime = $time["stop"];

                $u = SessionStreamer::where("starttime", ">=", $starttime)
                    ->where("stoptime", "<=", $stoptime)
                    ->where("status", SessionStreamer::CREATED_STATUS)
                    ->where("user_id", $id);
            }
        }

        if ($u != null) {
           /* $u->update(["status" => SessionStreamer::CREATING_MONTAGE_STATUS]);*/
            $linkCloudfront = config("aws.sourceLink");
            $montage = storage_path('montage');
            $min_video_montage = config("video.min_video_montage");
            $pathMontageCount = public_path() . "/montage_video/orange";
            $user = User::find($id);
            $avatar = $user->avatar;

            if(!$hasPrevious)
            {
                $same_live_stream = LiveStream::where('user_id',$u->user_id)->where(DB::raw('date_add(started_time, interval -30 minute)'),'<=',$u->starttime)->where(DB::raw('date_add(stopped_time, interval 30 minute)'),">=",$u->stoptime)->first();
                if ($same_live_stream){
                    Log::info("Have a LiveStream with duration of session, replace starttime {$same_live_stream->started_time} and replace stoptime {$same_live_stream->stopped_time}");
                    if ($u->starttime > $same_live_stream->started_time){
                        $u->starttime = $same_live_stream->started_time;
                    }
                    if ($u->stoptime < $same_live_stream->stopped_time){
                        $u->stoptime = $same_live_stream->stopped_time;
                    }
                }
                $number_boom = static::getTotalLikeBySession($u);
            }

            $datas = Video::getVideosByUser($id, $starttime, $stoptime);
            if($hasPrevious)
            {
                $sessions = $u->pluck('id')->toArray();
                $sessionId =  implode(",",$sessions);
                Log::info("create montage for multi sessions " . $sessionId);
            }
            $datas["session_id"] = $sessionId;
            Log::info("Username:" . $user->name ." session id: ". $sessionId ." start create montage with video numb: " . count($datas["videos"]));
            Log::info("Username: " . $user->name ." session id: ". $sessionId ." Start time : " . $starttime . " stop time: " . $stoptime);
            
            $path = $montage."/".$id."/";
            exec("cd " . $path . " && " . "rm *"); 
            $countVideo = count($datas["videos"]);
            if (count($datas["videos"]) >= $min_video_montage) {
                
                $u->update(["status" => SessionStreamer::CREATING_MONTAGE_STATUS]);

                $first = 0;
                $date = "";
                $size = "1280x720";
                $sar = "16:9";
                $audio2 = public_path()."/montage_video/silent2.mp3";
                $audio3 = public_path()."/montage_video/silent3.mp3";
                foreach ($datas["videos"] as $key => $video) {
                    Log::info("Username: " . $user->name ." session id: ". $sessionId ." create montage for video id: " . $video->id);
                    $sponsorship = Sponsorship::where("user_id", $video->user_id)
                                   ->where("starttime", "<=", $video->created_at)
                                   ->where("expiredtime", ">=", $video->created_at)
                                   ->first();
                    $timeCut = 2;
                    if($sponsorship == null)
                    {
                        $timeCut = 2;
                    }
                    else
                    {
                        $timeCut = $sponsorship->duration;
                    }
                    $videoPath = FFmpegHelper::downloadVideo($linkCloudfront.$video->links3, $id, $timeCut);
                    if($first == 0)
                    {
                        $textIntro = array();
                        $textIntro["username"] = $user->displayname;
                        $textIntro["top"] = "Top " . $countVideo . " replays";
                        $textIntro["date"] = Video::getDateMontage($starttime);
                        Log::info("stop session : " . $starttime ." convert to ". $textIntro["date"]);
                        $montageCount = FFmpegHelper::convertVideoCount($videoPath);
                        $pathMontageCount = $montageCount["path"] . "/orange";
                        $size = $montageCount["size"];
                        $audio3 = $montageCount["pathAudio3"];
                        $audio2 = $montageCount["pathAudio2"];
                        $sar = $montageCount["sar"];
                        FFmpegHelper::createIntro($textIntro, $path, $size, $avatar, $linkCloudfront.$video->thumbnail, $audio3);
                    }
                    if($videoPath)
                    {
                        $videoCount = $pathMontageCount.$countVideo.".mp4";
                        $res = FFmpegHelper::addImageBeforeVideo($path, $videoCount, $videoPath, $video->requested_by, $size, $audio2, $sar);
                        $countVideo--;
                    }
                    $first++;
                    $video->created_montage = 1;
                    $video->save();
                }
                $datas["userId"] = $id;
                if ($datas["videos"] != null) {
                    $pathMontage = FFmpegHelper::concatVideos($id);
                    /*$pathMontage = "/var/www/afkvr/server/storage/montage/58/montage.mp4";*/
                    $resultUpload = FFmpegHelper::uploadMontage($pathMontage, $datas);
                    //User::where("id", $id)->update(["lasttime" => $u->stoptime]);
                    if($hasPrevious)
                    {
                        $u = SessionStreamer::where("starttime", ">=", $starttime)
                            ->where("stoptime", "<=", $stoptime)
                            ->where("status", SessionStreamer::CREATING_MONTAGE_STATUS)
                            ->where("user_id", $id);
                    }
                    if($resultUpload)
                    {
                        $u->update(["status" => SessionStreamer::CREATED_MONTAGE_STATUS]);
                    }
                    else
                    {
                        $u->update(["status" => SessionStreamer::CREATED_MONTAGE_FAIL_STATUS]);
                    }
                    Log::info("Username: " . $user->name ." session id: ". $sessionId ." create monate done!");
                }
            }
            else
            {
                Log::info("Username: " . $user->name ." session id: ". $sessionId ." Not enough videos number");
            }
        }
    }

    public static function dispatchCreateUserVideoMontageJob($session)
    {
        $job = (new CreateUserVideoMontage($session))->onQueue('UserVideoMontage')->delay(config('live-stream.time_between_session'));
        dispatch($job);
    }

    public static function dispatchCreateUserVideoMontageJobWithoutDelay($session)
    {
        $job = (new CreateUserVideoMontage($session))->onQueue('UserVideoMontage');
        dispatch($job);
    }

    public static function getDateMontage($timestamp, $format = 'F j, Y', $timezone = "PST")
    {
        $date = Carbon::createFromFormat("Y-m-d H:i:s", $timestamp, 'UTC');
        $d = $date->tz('PST');
        return $d->format($format);  
    }

    public static function followMontages()
    {
        $currentDay = Carbon::now();
        $start = $currentDay->startOfDay()->subHours(8)->toDateTimeString();
        $currentDay2 = Carbon::now();
        $end = $currentDay2->endOfDay()->subHours(8)->toDateTimeString();
        Log::info("start : " . $start);
        Log::info("end : " . $end);
        $numberOfMontagesCreated = 
         Video::where("created_at", ">=", $start)
        ->where("created_at", "<=", $end)
        ->where("session_id", "!=", "")->count();

        $numberOfMontagesSentMail = Video::where("created_at", ">=", $start)
        ->where("created_at", "<=", $end)
        ->where("sent_mail_montage", Video::SENT_MAIL_MONTAGE_TOP)->count();
        $emailFrom = config("mail.follow_montage.sender.senderMail");
        $subject = config("mail.follow_montage.subject") . 
        " " . $currentDay->toDateString();
        $sendername = config("mail.follow_montage.sender.sendername");
        $emailTo = config("mail.follow_montage.list_send_mail")[0];
        $emailCc = config("mail.follow_montage.list_send_mail")[1];
        $info = array();
        $info["sender"] = config("mail.follow_montage.sender.sendername");
        $info["temp"] = "emails.send_follow_montage";
        $info["numberOfMontagesCreated"] = $numberOfMontagesCreated;
        $info["numberOfMontagesSentMail"] = $numberOfMontagesSentMail;
        Helper::sendMailCc($emailFrom, $emailTo, $emailCc, $subject, $info);
    }
}
