<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;

class ViewDay extends Model
{
    protected $table = 'view_day';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'video_id', 'view_numb', 'date', 'created_at', 'updated_at'];

    public static function updateByDay($video_id, $view_numb=1, $isSetView = false)
    {
    	$date  = Carbon::now()->format('Y-m-d');
    	$viewDay = ViewDay::where("date", $date)
    			   ->where("video_id", $video_id)->first();
    	if($viewDay != null) 
    	{
    	    if($isSetView)
            {
                $viewDay->view_numb = $view_numb;
            }
            else
            {
                $viewDay->view_numb = $viewDay->view_numb + $view_numb;
            }
            $viewDay->save();
    	}
    	else
    	{
    		$new = new ViewDay();
    		$new->video_id = $video_id;
    		$new->view_numb = $view_numb;
    		$new->date = $date;
    		$new->save();
    	}
    	$viewWeek = ViewDay::where("video_id", $video_id)->where("created_at",">=",Carbon::yesterday())->sum("view_numb");
    	return $viewWeek;
    }
    public static function clearViewDay()
    {
        $start_time = microtime(true);

        ViewDay::where("created_at","<",Carbon::now()->subWeek())->delete();

        $ex_time = microtime(true) - $start_time;
        Log::info("clearViewDay time execute : ".$ex_time);
    }
}