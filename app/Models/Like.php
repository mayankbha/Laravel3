<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Video;
use Log;

class Like extends Model
{
    protected $table = 'likes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'video_id', 'like_state', 'created_at', 'updated_at'];

    public static function updateLike($userId, $videoId)
    {
    	$user = User::find($userId);
    	$like = Like::where("user_id", $userId)
    			->where("video_id", $videoId)->first();
    	$increase = true;
    	$super = false;
    	if($user->is_super == User::SUPER_USER)
    	{
    		$super = true;
    	}
    	if($like != null) 
    	{
    		$like->update(array("like_state" => !$like->like_state));
    		if($like->like_state == 0)  $increase = false;
    	}
    	else
    	{
    		$like = new Like();
    		$like->user_id = $userId;
    		$like->video_id = $videoId;
    		$like->like_state = 1;
    		$like->save();
    	}
    	$like->updateSuperLike($userId, $videoId, $increase, $super);
    }

    public static function updateSuperLike($userId, $videoId, $increase, $super)
    {
    	$video = Video::find($videoId);
    	if($video != null)
    	{
    		if($super)
    		{
    			if($increase) $video->like_super = $video->like_super + 1;
    			else 
    			{
    				if($video->like_super > 0)
    				{
    					$video->like_super = $video->like_super - 1;
    				}
    			}
                $video->super_like_time = date('Y-m-d H:i:s');
    			$video->save();
    		}
    	}	
    }

    
}