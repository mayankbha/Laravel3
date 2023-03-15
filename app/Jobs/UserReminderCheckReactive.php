<?php

namespace App\Jobs;

use App\Jobs\Job;
use Carbon\Carbon;
use Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\User;
use App\Models\UserReminder;
use App\Models\UserReminderMailLog;
use App\Models\Video;

class UserReminderCheckReactive extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user_reminder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_reminder)
    {
        //
        $this->user_reminder = $user_reminder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $start_of_last_day = Carbon::now()->subHours(24);
        $end_of_last_day = Carbon::now();

        $check_video_of_user = Video::where('user_id',$this->user_reminder->user_id)->where('created_at','>=',$start_of_last_day)->where('created_at','<',$end_of_last_day)->first();
        $max_number_of_checking_reactive = config('reminder.max_number_checking_reactive')[$this->user_reminder->current_template-1];
        Log::info("[UserReminderCheckReactive] max_number_of_checking_reactive: $max_number_of_checking_reactive");
        if ($check_video_of_user){
            Log::info("[UserReminderCheckReactive] user reactive {$this->user_reminder->user_id}");
            $this->user_reminder->current_status = UserReminder::CURRENT_STATUS_USER_REACTIEVE;
            $this->user_reminder->save();
            return true;
        }
        else{
            if ($this->user_reminder->interval_status < $max_number_of_checking_reactive){
                $this->user_reminder->interval_status++;
                $this->user_reminder->save();
                $userReminderCheckReactiveJob = (new UserReminderCheckReactive($this->user_reminder))->onQueue("userReminder")->delay(config('reminder.checking_reactive_interval'));
                dispatch($userReminderCheckReactiveJob);
                Log::info("[UserReminderCheckReactive] delay job check day {$this->user_reminder->interval_status} {$this->user_reminder->user_id}");
                return true;
            }
            else{
                if ($this->user_reminder->current_template < config('reminder.max_template')){
                    $this->user_reminder->interval_status = 0;
                    $userReminderMailLog = new UserReminderMailLog([
                        'user_id' => $this->user_reminder->user_id,
                        'user_reminder_id' => $this->user_reminder->id,
                        'template_id' => $this->user_reminder->current_template,
                        'email' => $this->user_reminder->email,
                        'current_status' => UserReminderMailLog::CURRENT_STATUS_CREATED,
                    ]);
                    $userReminderMailLog->save();
                    $sendUserReminderMailJob = (new SendUserReminderMail($userReminderMailLog))->onQueue("userReminderEmail");
                    dispatch($sendUserReminderMailJob);
                    Log::info("[UserReminderCheckReactive] send email with new template {$this->user_reminder->current_template} {$this->user_reminder->user_id}");
                    $this->user_reminder->current_template++;
                    $userReminderCheckReactiveJob = (new UserReminderCheckReactive($this->user_reminder))->onQueue("userReminder")->delay(config('reminder.checking_reactive_interval'));
                    dispatch($userReminderCheckReactiveJob);
                    Log::info("[UserReminderCheckReactive] delay job check day {$this->user_reminder->interval_status} {$this->user_reminder->user_id}");
                    $this->user_reminder->save();
                    return true;
                }
                else{
                    Log::info("[UserReminderCheckReactive] Complete remind {$this->user_reminder->user_id}");
                    $this->user_reminder->current_status = UserReminder::CURRENT_STATUS_COMPLETE_REMIND;
                    $this->user_reminder->save();
                    return true;
                }
            }
        }

    }
}
