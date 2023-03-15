<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\StartUserReminder;
use Carbon\Carbon;
use DB;
use Log;

class UserReminder extends Model
{
    //
    protected $fillable = ['user_id','email','type','timezone','send_at','current_status','current_template','interval_status','first_sent_at'];

    const TYPE_CHURN = 'churn';
    const CURRENT_STATUS_START_REMIND = 0;
    const CURRENT_STATUS_PROCESSING_REMIND = 1;
    const CURRENT_STATUS_COMPLETE_REMIND = 2;
    const CURRENT_STATUS_USER_REACTIEVE = 10;

    public function user(){
        return $this->belongsTo('App\Models\User',"user_id","id");
    }

    public static function dispatchStartUserReminderJob($user_reminder){
        $job = (new StartUserReminder($user_reminder))->onQueue("userReminder");
        dispatch($job);
        return true;
    }

    public function currentStatusToString(){
        if ($this->current_status == static::CURRENT_STATUS_START_REMIND){
            return 'Pending';
        }
        if ($this->current_status == static::CURRENT_STATUS_PROCESSING_REMIND){
            return 'Processing';
        }
        if ($this->current_status == static::CURRENT_STATUS_COMPLETE_REMIND){
            return 'Complete';
        }
        if ($this->current_status == static::CURRENT_STATUS_USER_REACTIEVE){
            return 'User reactive';
        }
    }

    /*Get user who we sent email to in churn comes back within next 7 days */
    public static function getUserComeback($startDayOfWeek, $endDayOfWeek)
    {
        //DB::enableQueryLog();
        Log::info("startDayOfWeek: " . $startDayOfWeek);
        Log::info("endDayOfWeek: " . $endDayOfWeek);
        $reminder = UserReminder::join('users', 'users.id', '=', 'user_reminders.user_id')
        ->join('social_accounts', 'users.id', '=', 'social_accounts.user_id')
        ->select("user_reminders.id", "users.email", "user_reminders.updated_at",
            "user_reminders.user_id", "user_reminders.first_sent_at",
            "user_reminders.current_template", "users.name", "social_accounts.follower_numb")
        ->where("current_status", UserReminder::CURRENT_STATUS_USER_REACTIEVE)
        ->where("user_reminders.updated_at", ">=", $startDayOfWeek)
        ->where("user_reminders.updated_at", "<=", $endDayOfWeek)
        ->orderBy("user_reminders.updated_at", "asc")
        ->get();
        /*Log::info(
            DB::getQueryLog()
        );*/
        return ["data" => $reminder, "start" => $startDayOfWeek, "end" => $endDayOfWeek];
    }

    public static function getDataUserComeBack()
    {
        $datas = [];
        $startDay = config("reminder.start_report_user_comeback");
        $start = Carbon::parse($startDay);
        $startDayOfWeek = $start->toDateString() . " 00:00:00";
        $end = $start->addDays(7);
        $endDayOfWeek = $end->toDateString(). " 23:59:59";
        $datas[] = UserReminder::getUserComeback($startDayOfWeek, $endDayOfWeek);
        $countWeek = $start->diffInWeeks(Carbon::now()); 
        //Carbon::now()->toDateString();
        for($i = 1; $i <= $countWeek+1; $i++)
        {
            $start = $end->addDays(1);
            $startDayOfWeek = $start->toDateString() . " 00:00:00";
            $end = $start->addDays(7); 
            $endDayOfWeek = $end->toDateString() . " 23:59:59";
            $datas[] = UserReminder::getUserComeback($startDayOfWeek, $endDayOfWeek);
        }
        return array_reverse($datas);
    }

    public function getLastSendMail()
    {
        $mailLog = UserReminderMailLog::where('user_reminder_id', $this->id)
                ->orderBy("created_at", "desc")->first();
        if($mailLog != null)
        {
            return $mailLog->created_at;
        }
        return "";
    }
}

