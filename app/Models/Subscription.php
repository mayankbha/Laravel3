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

class Subscription extends Authenticatable
{
    protected $table = 'subscriptions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const SUBSCRIBED  = 1;
    const UNSUBSCRIBED = 0;

    protected $fillable = [
        'id', 'streamer_id', 'subscriber_id', 'status', 'created_at', 'updated_at'
    ];

    public function streamer()
    {
        return $this->belongsTo('App\Models\User','streamer_id');
    }
    
    public function subscriber()
    {
        return $this->belongsTo('App\Models\User','subscriber_id');
    }

    function subscribe(Request $request){
        $subscription = new Subscription();
        $subscription->streamer_id=$request->input('streamer_id');
        $subscription->subscriber_id=Auth::id();
        $subscription->status=1;
        $subscription->save();
        return response()->json(['status'=>1]);
    }
}