<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BoomMeter;
use App\Models\User;
use Carbon\Carbon;

class SessionBoomMeter extends Model
{
    protected $table = 'session_boom_meter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'boom_meter_type_id', 'starttime', 'stoptime'];

    public static function saveSession($userCode, $boomMeterTypeId)
    {
        $user = User::where("code", $userCode)->first();
        if($user != null)
        {
            $userId = $user->id;
            $starttime = Carbon::now();
            $newSession = new SessionBoomMeter();
            $newSession->user_id = $userId;
            $newSession->boom_meter_type_id = $boomMeterTypeId;
            $newSession->starttime = $starttime;
            $endSession = SessionBoomMeter::where("user_id", $userId)->orderBy("created_at", "desc")->first();
            if($endSession != null)
            {
                $endSession->stoptime = $starttime;
                $endSession->save();
            }
            $newSession->save();
        }
        
    }
}