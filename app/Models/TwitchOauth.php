<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitchOauth extends Model
{
    protected $table = 'twitchoauth';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'expires_in_tw', 'access_token_tw', 'refresh_token_tw', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}