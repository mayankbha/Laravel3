<?php
/**
 * Created by PhpStorm.
 * User: tannn
 * Date: 4/17/17
 * Time: 5:03 PM
 */
namespace App\Handlers\Events;

use App\Models\Video;
use Log;
use App\Jobs\ProcessVideoUpdate;
use App\Models\VideoGame;
use Lang;
use Redis;
class VideoEvent {


    public function __construct()
    {

    }

    public function created(Video $video)
    {
        $job = (new ProcessVideoUpdate($video,"Created"))->onQueue('VideoUpdated');
        dispatch($job);
    }

    public function updated(Video $video,$dirty)
    {
        $job = (new ProcessVideoUpdate($video,"Updated",null,$dirty))->onQueue('VideoUpdated');
        dispatch($job);
    }

    public function updatedVideoGame(VideoGame $videoGame){
        $video = Video::where('id',$videoGame->video_id)->get()->first();
        $job = (new ProcessVideoUpdate($video,"VideoGameUpdated",$videoGame))->onQueue('VideoUpdated');
        dispatch($job);
    }

    public function updatingVideoGame(VideoGame $videoGame){
        $old_video_game_object = VideoGame::where('id',$videoGame->id)->get()->first();
        Log::info($old_video_game_object->game_id);
        $video_game_key_cached = Lang::get("cached.game",array('gamename'=>$old_video_game_object->game_id));
        Redis::zremrangebyscore($video_game_key_cached,$videoGame->video_id,$videoGame->video_id);
    }

    public function createdVideoGame(VideoGame $videoGame){
        $video = Video::where('id',$videoGame->video_id)->get()->first();
        $job = (new ProcessVideoUpdate($video,"VideoGameCreated",$videoGame))->onQueue('VideoUpdated');
        dispatch($job);
    }

    public function deletedVideoGame(VideoGame $videoGame){
        $video = Video::where('id',$videoGame->video_id)->get()->first();
        $job = (new ProcessVideoUpdate($video,"VideoGameDeleted"))->onQueue('VideoUpdated');
        dispatch($job);
    }

    // Other Handlers/Methods...
}