<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hash;

class Rating extends Model
{
    protected $table = 'ratings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'video_id', 'cookie_code', 'numb_star', 'created_at', 'updated_at'];

    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }

    public static function createOrUpdateStar($data)
    {
        $datetime = date('Y-m-d H:i:s');
        $time = strtotime($datetime);
        $rate_time_limit = config('video.rate_time_limit');
        $time = $time - ($rate_time_limit * 60);
        $datetimeBefore = date("Y-m-d H:i:s", $time);
        $rating = Rating::where("cookie_code", $data["cookie_code"])
                  ->where("created_at" , ">=", $datetimeBefore)
                  ->first();
        $createNewCookie = true;
        if($rating == null)
        {
            $rating = new Rating();
            $rating->video_id = $data["video_id"];
            $rating->numb_star = $data["numb_star"];
            $rating->cookie_code = Hash::make($data["video_id"] . "_" . $data["ip"] . "_" . time());
            $rating->save();
        }
        else
        {
            $createNewCookie = false;
            $rating->numb_star = $data["numb_star"];
            $rating->save();
        }
        $video = Video::find($data["video_id"]);
        $video->rateavg = $video->getAvgRating();
        $video->save();
        return array("createNewCookie" => $createNewCookie, "cookie" => $rating->cookie_code);
    }
}