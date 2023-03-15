<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoomEvent extends Model
{
    protected $table = "events";
    //

    protected $fillable = [
        'id', 'user_id', 'view_numb','like_numb'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
