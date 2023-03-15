<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionStreamer extends Model
{
    protected $table = 'session';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'starttime', 'stoptime', 'created_at', 'updated_at', 'status'];
    const CREATED_STATUS = 0;
    const CREATING_MONTAGE_STATUS = 1;
    const CREATED_MONTAGE_STATUS = 2;
    const CREATED_MONTAGE_FAIL_STATUS = 3;
}