<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TwitchApi;
use Log;
use GuzzleHttp\Client;

class SocialConnected extends Model
{
    protected $table = 'social_connected';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'auto_tweet', 'type', 'token', 'token_secret','email','created_at', 'updated_at', 'name',
        'nick_name', 'avatar', 'social_id'];
}