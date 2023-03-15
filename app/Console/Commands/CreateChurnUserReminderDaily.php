<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Models\UserReminder;
use DB;
use App\Models\User;
use Log;

class CreateChurnUserReminderDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-churn-user-reminder-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $sub_week = Carbon::now()->subDays(7)->startOfDay();
        $sub_8_days = Carbon::now()->subDays(8)->startOfDay();
        $user_reminders_exist = UserReminder::where('created_at','<=',Carbon::now())->first();
        if ($user_reminders_exist->count() == 0){
            $query = "SELECT u.* FROM ( SELECT users.id AS id,users.name as name,users.email as email,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS video_count,max(videos.created_at) as lattest_video_created FROM users, videos WHERE videos.user_id = users.id GROUP BY users.id ) AS u WHERE u.video_count >= 2 AND u.is_streamer = 1 and lattest_video_created < '{$sub_week}' ORDER BY u.id ASC ";

            $results = DB::select(DB::raw($query));
            $results = json_decode(json_encode($results), true);

            foreach ($results as $row) {
                if ($row['email'] == ""){
                    continue;
                }
                $user_reminder_exist = UserReminder::where('user_id',$row['id'])->where('current_status','!=',UserReminder::CURRENT_STATUS_USER_REACTIEVE)->first();
                if ($user_reminder_exist){
                    continue;
                }
                $user_reminder = $this->saveUserReminder($row);
                UserReminder::dispatchStartUserReminderJob($user_reminder);
            }
            Log::info("[churnUser] first create daily list");
        }
        else{

            $query = "SELECT u.* FROM ( SELECT users.id AS id,users.name as name,users.email as email,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS video_count,max(videos.created_at) as lattest_video_created FROM users, videos WHERE videos.user_id = users.id GROUP BY users.id ) AS u WHERE u.video_count >= 2 AND u.is_streamer = 1 and lattest_video_created < '{$sub_week}' and lattest_video_created >= '{$sub_8_days}' ORDER BY u.id ASC ";
            $results = DB::select(DB::raw($query));
            $results = json_decode(json_encode($results), true);
            foreach ($results as $row) {
                if ($row['email'] == ""){
                    continue;
                }
                $user_reminder_exist = UserReminder::where('user_id',$row['id'])->where('current_status','!=',UserReminder::CURRENT_STATUS_USER_REACTIEVE)->first();
                if ($user_reminder_exist){
                    continue;
                }
                $user_reminder = $this->saveUserReminder($row);
                UserReminder::dispatchStartUserReminderJob($user_reminder);
            }
            Log::info("[churnUser] create daily list");
        }

    }

    private function saveUserReminder($row){
        $user = User::find($row['id']);
        $user_reminder = new UserReminder();
        $user_reminder->user_id = $row['id'];
        $user_reminder->last_video_created_at = $row['lattest_video_created'];
        $user_reminder->user_created_at = $row['created_at'];
        $user_reminder->number_video = $row['video_count'];
        $user_reminder->email = $row['email'];
        $user_reminder->type = "churn";
        $user_reminder->timezone = $user->timezone;
        $user_reminder->sent_at = "07:00:00";
        $user_reminder->current_status = UserReminder::CURRENT_STATUS_START_REMIND;
        $user_reminder->current_template = 0;
        $user_reminder->interval_status  = 0;
        $user_reminder->save();
        return $user_reminder;
    }
}
