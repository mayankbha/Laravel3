<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAdminPermission extends Model
{
    //

    protected $table = "user_admin_permission";

    protected $fillable = ['user_id','permission'];
}
