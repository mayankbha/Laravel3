<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAppVersion extends Model
{
    //
    protected $fillable = ['user_id','version'];
}
