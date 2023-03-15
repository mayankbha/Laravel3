<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class Sponsorship extends Model
{
    protected $table = 'sponsorship';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'starttime', 'expiredtime', 'video_link', 'duration', 'timezone', 'status'];

    const ACTIVE_STATUS = 0;
    const EXPIRE_STATUS = 1;
    const FUTURE_STATUS = 2;
    const DELETED_STATUS = -1;

    public function getStatus()
    {
    	if($this->status == Sponsorship::DELETED_STATUS) 
    	{
    		return Sponsorship::DELETED_STATUS;
    	}
    	else
    	{
    		$currentDate = Carbon::now();
	    	$start = Carbon::parse($this->starttime);
	    	$expire = Carbon::parse($this->expiredtime);
	    	if($currentDate->lt($start))
	    	{
	    		return Sponsorship::FUTURE_STATUS;
	    	}
	    	elseif ($currentDate->gt($start) && $currentDate->lt($expire)) 
	    	{
	    		return Sponsorship::ACTIVE_STATUS;
	    	}
	    	elseif ($currentDate->gt($expire)) 
	    	{
	    		return Sponsorship::EXPIRE_STATUS;
	    	}
    	}
    }
    public function deleteSponsorship()
    {
        $currentStatus = $this->getStatus();
        if($currentStatus == Sponsorship::EXPIRE_STATUS)
        {
            $this->status = Sponsorship::DELETED_STATUS;
            $this->save();
        }
        elseif($currentStatus == Sponsorship::ACTIVE_STATUS)
        {
            $this->status = Sponsorship::DELETED_STATUS;
            $this->expiredtime = Carbon::now()->toDateTimeString();
            $this->save();
        }
        else
        {
            $this->delete();
        }
    }
}