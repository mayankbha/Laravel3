<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Game;
use Log;
use App\Models\Video;
use Illuminate\Support\Facades\Event;

class VideoGame extends Model
{
    protected $table = 'video_game';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'video_id', 'game_id','created_at', 'updated_at'];

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }
    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }

    public static function boot() {

        parent::boot();

        static::created(function($videoGame) {
            Event::fire('VideoGame.created', $videoGame);
        });

        static::updated(function($videoGame) {
            Event::fire('VideoGame.updated', $videoGame);
        });

        static::updating(function($videoGame) {
            Event::fire('VideoGame.updating', $videoGame);
        });

        static::deleted(function($videoGame) {
            Event::fire('VideoGame.deleted', $videoGame);
        });
    }

    public static function createRelations($gameIds, $videoId)
    {
        foreach ($gameIds as $key => $gameId) 
        {
            VideoGame::create(['game_id' => $gameId, 'video_id' => $videoId]);
        }
    }
    public static function updateCategoryGames()
    {
        $start_time = microtime(true);

        Log::info("start update category name");
        $games = VideoGame::select("video_game.game_id",DB::raw('count(*) as total'))
                    ->join('videos','videos.id','=','video_game.video_id')
                    ->where('videos.status','=',1)
                    ->groupby("game_id")
                    ->having('total',">=",10)
                    ->get();
        Game::where('is_category',1)->update(['is_category'=>0]);
        foreach ($games as $game) {
            Log::info("update category for game : " . $game->game_id);
            $g = Game::where("id", $game->game_id)->update(["is_category" => 1,"video_count"=>$game->total]);
        }
        Log::info("end update category name");
        $ex_time = microtime(true) - $start_time;

        Log::info("updateCategoryGames time execute : ".$ex_time);
    }

    public static function remove_invalid_video_game(){
        VideoGame::select("*")->chunk(100,function ($video_game){
            foreach ($video_game as $item){
                $video = Video::where('id',$item->video_id)->get()->first();
                if (!$video){
                    print_r($item->video_id . "\n");
                    $item->delete();
                }
            }
        });
    }
}