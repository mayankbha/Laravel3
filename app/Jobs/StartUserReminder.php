<?php

namespace App\Jobs;

use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\UserReminder;
use App\Models\UserReminderMailLog;
use App\Models\User;
use App\Jobs\UserReminderCheckReactive;
use App\Jobs\SendUserReminderMail;
use Log;

class StartUserReminder extends Job implements ShouldQueue
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
        Log::info("[userReminder] start user reminder {$this->user_reminder->user_id} {$this->user_reminder->id} {$this->user_reminder->email}");
        if ($this->user_reminder->current_status == UserReminder::CURRENT_STATUS_START_REMIND){
            $userReminderCheckReactiveJob = (new UserReminderCheckReactive($this->user_reminder))->onQueue("userReminder")->delay(config('reminder.checking_reactive_interval'));
            dispatch($userReminderCheckReactiveJob);
            Log::info("[UserReminderCheckReactive] delay job check day {$this->user_reminder->interval_status} {$this->user_reminder->user_id}");
        }

        if ($this->user_reminder->current_status ==  UserReminder::CURRENT_STATUS_START_REMIND){
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
            if ($this->user_reminder->current_template < config('reminder.max_template')){
                $this->user_reminder->current_template = $this->user_reminder->current_template + 1;
            }
            $this->user_reminder->first_sent_at = Carbon::now();
        }
        $this->user_reminder->current_status = UserReminder::CURRENT_STATUS_PROCESSING_REMIND;
        $this->user_reminder->save();
        return true;
    }
}
