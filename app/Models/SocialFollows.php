<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Video;
use App\Helpers\Helper;
use Log;
use Lang;
use Cache;
use App\Jobs\FollowStreamer;
use App\Models\SocialConnected;
use Carbon\Carbon;

class SocialFollows extends Authenticatable
{
    protected $table = 'social_follows';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id', 'user_id', 'social_account_type', 'recommended_streamer_id', 'created_at', 'updated_at'
    ];

    public function recommendedstreamer()
    {
        return $this->belongsTo('App\Models\User','recommended_streamer_id');
    }
	
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}