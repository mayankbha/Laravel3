<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Mail;
use Log;

class SendAlertEmailMonday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:send-alert-email-monday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send alert email at monday';

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
        $sub_week = Carbon::now()->subDays(5);
        $sub_10day = Carbon::now()->subDays(10);
        $query = "select 
                    us.name,us.email,us.subscriber_numb, us.created_at as streamer_created_at ,v.last_created_at,v.number_video
                  from 
                    (SELECT u.name as name,u.email as email,u.id as id,sc.subscriber_numb as subscriber_numb,u.created_at as created_at FROM `users` as u,social_accounts as sc where u.id = sc.user_id and sc.subscriber_numb > 0 and u.created_at > '{$sub_10day}' order by sc.subscriber_numb desc) as us left join 
                    (select user_id as user_id,count(id) as number_video,max(created_at) as last_created_at from videos where 1 group by user_id order by id desc ) as v 
                  on v.user_id = us.id 
                  where us.subscriber_numb >= 100 and (v.last_created_at < '{$sub_week}' or v.last_created_at is null)
                  group by us.id 
                  order by us.subscriber_numb desc";
        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $date = Carbon::now()->toDateString();
        $file_path = $dir_path . "list-streamers-who-more-than-100-subcriber-and-have-not-uploaded-video-for-5-days-{$date}.csv";
        $file = fopen($file_path, 'w');
        $first = 1;
        foreach ($results as $row) {
            if ($first) {
                $array_key = [];
                foreach ($row as $key => $value) {
                    $array_key[] = $key;
                }
                fputcsv($file, $array_key);
                $first = 0;
            }
            fputcsv($file, $row);
        }
        fclose($file);
        if (config('mail.alertState') && count($results)){
            Log::info("Start send mail alert");
            $data['datetime'] = Carbon::now();
            $data['to'] = config("mail.sendMailAlert.sendTo");
            $data['from'] = 'sumit@boom.tv';
            $data['cc'] = config("mail.sendMailAlert.emailCc");
            $data['subject'] = "List streamers who has more than 100 subcriber and have not uploaded a video for > 5 days";
            $data['file_path'] = $file_path;
            try {
                Mail::send('emails.alert_inactive_streamer',$data, function ($message) use ($data) {
                    $message->from($data['from'], 'boom.tv');

                    $message->to($data['to'])->cc($data['cc'])->subject($data['subject']);
                    $message->attach($data['file_path'], []);
                });
                Log::info("Send mail alert success");
            } catch (\Exception $e) {
                Log::info("Send mail alert error");
            }

        }
    }
}
