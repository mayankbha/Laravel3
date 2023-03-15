<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Image extends Model
{
    protected $table = 'images';
    const FILTER_NONE = 0;
    const FILTER_STREAMER = 1;
    const FILTER_CHANNEL = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'user_id', 'description', 'paths3', 'code', 'created_at', 'updated_at', 'channel', 'channel', 'channel_id'];

    public function imageChannel()
    {
        return $this->belongsTo('App\Models\ImageChannel', 'channel_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public static function getImgByCondition($filterBy, $limit, $offset, $streamer, $channelId)
    {
    	$query = Image::query();
        if($offset == "" || !is_numeric($offset))
        {
            $offset = 0;
        }
        if($filterBy == Image::FILTER_STREAMER && $streamer != "")
        {
        	$query->where("channel", $streamer);
        }
        if($filterBy == Image::FILTER_CHANNEL && $channelId != "")
        {
            $query->where("channel_id", $channelId);
        }
        $images = $query->with("imageChannel")
                    ->with("user")
                    ->orderby("created_at", "desc")->take($limit)->offset($offset)->get();

        return $images;
    }
    
    public function getUserFormChannel()
    {
        $user = User::where("name", $this->channel)->first();
        return $user;
    }
}