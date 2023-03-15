<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewerUserSub extends Model
{
    //
    protected $table = "viewer_user_sub";
    protected $fillable = ['viewer_id','user_id'];
}
