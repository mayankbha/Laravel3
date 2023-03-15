<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UninstallInfo extends Model
{
    protected $table = 'uninstall_info';

    protected $fillable = [
        'id', 'user_id', 'version','time','created_at', 'updated_at'];

}
