<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BoomMeter;
use App\Models\User;
use Carbon\Carbon;

class BoomMeterType extends Model
{
    protected $table = 'boom_meter_type';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'type', 'name', 'description', 'folders3'];

    const UNLOCK_ACTION = 1;
    const INSTALL_ACTION = 2;

    const DEFAULT_BASIC_TYPE = -1;
    const DEFAULT_TYPE = 0;
    const DEFAULT_LOCK_TYPE = 1;
    const CUSTOM_TYPE = 2;

    public function getName()
    {
        return str_replace(' ', '', $this->name);
    }
}