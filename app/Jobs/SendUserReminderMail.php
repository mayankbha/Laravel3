<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\UserReminderMailLog;
use App\Helpers\Helper;
use Log;
use App\Models\UnsubscriberEmail;

class SendUserReminderMail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user_reminder_mail_log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_reminder_mail_log)
    {
        //
        $this->user_reminder_mail_log = $user_reminder_mail_log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = User::where('id',$this->user_reminder_mail_log->user_id)->first();
        if (config('app.env') == "local"){
            $recipients =   [
                [
                    'address' => [
                        'name' => $user->name,
                        'email' => "tan.nn@boom.tv",
                    ],
                ],
            ];
        }
        else{
            $recipients =   [
                [
                    'address' => [
                        'name' => $user->name,
                        'email' => $this->user_reminder_mail_log->email,
                    ],
                ],
            ];
        }

        $template_id = "we-miss-you-1";
        switch ($this->user_reminder_mail_log->template_id){
            case 0 :
                $template_id = "we-miss-you-1";
                break;
            case 1 :
                $template_id = "we-miss-you-2";
                break;
            case 2 :
                $template_id = "we-miss-you-3";
                break;
            case 3 :
                $template_id = "we-miss-you-4";
                break;
            default :
                break;
        }

        Log::info("[sendUserReminderMail] ready for send mail: $template_id {$user->id} {$user->email} reminder_id: {$this->user_reminder_mail_log->user_reminder_id}");
        if (config('app.env') == "boom-admin" || config('app.env') == "local"){
            $data = [
                    'email'=>$this->user_reminder_mail_log->email,
                    'user_id'=>$this->user_reminder_mail_log->user_id,
                    'reminder_id' => $this->user_reminder_mail_log->user_reminder_id,
                    'unsubscribe' => UnsubscriberEmail::getUnsubscriberEmailLink($user->code,0),
            ];
            if (UnsubscriberEmail::checkIfCanSendEmail($user,0)){
                $transmission_id = Helper::sendRemindeMailBySparkPostTemplate($template_id,$data,$recipients,[]);
                $this->user_reminder_mail_log->current_status = UserReminderMailLog::CURRENT_STATUS_SENT;
                $this->user_reminder_mail_log->transmission_id = $transmission_id;
            }
            else{
                $this->user_reminder_mail_log->current_status = UserReminderMailLog::CURRENT_STATUS_UNSUBSCRIBE;
                Log::info("[SendUserReminderMail] email did not sent, user unsubscribe boom");
            }
        }

        $this->user_reminder_mail_log->save();
        return true;
    }
}
