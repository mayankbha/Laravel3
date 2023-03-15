<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Mail;
use Log;
use App\Helpers\AWSHelper;

class CreateAdminReportDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-admin-report-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin report daily';

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

        $this->createInactiveStreamer();

        $this->createDailyActiveStreamers();
    }

    public function createInactiveStreamer(){
        $sub_week = Carbon::now()->subWeek();
        /*$query = "SELECT u.* FROM ( SELECT users.id AS id,users.name as name,users.email as email,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS video_count,max(videos.created_at) as lattest_video_created FROM users, videos WHERE videos.user_id = users.id GROUP BY users.id ) AS u WHERE u.video_count >= 2 AND u.is_streamer = 1 and lattest_video_created < '{$sub_week}' ORDER BY u.id DESC ";*/

        $query = "SELECT u.*,
                (SELECT social_accounts.follower_numb FROM social_accounts WHERE  social_accounts.user_id = u.id) as follower_numb,
                (SELECT max(session.stoptime) FROM session WHERE  session.user_id = u.id) as last_seen
                 FROM ( SELECT users.id AS id,users.name as name,users.email as email,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS video_count,max(videos.created_at) as lattest_video_created FROM users, videos WHERE videos.user_id = users.id GROUP BY users.id ) AS u WHERE u.video_count >= 2 AND u.is_streamer = 1 and lattest_video_created < '{$sub_week}' ORDER BY u.id DESC ";

        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        echo "\n Query success! \n";
        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $date = Carbon::now()->toDateString();
        $file_path = $dir_path . "list-streamer-who-uploaded-video-dont-have-activity-in-subweek-{$date}.csv";
        $file = fopen($file_path, 'w');
        $first = 1;
        echo "\n Write start! \n";
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
        if (config('app.env') == 'boom-admin'){
            $link = AWSHelper::uploadReportToS3($file_path,'',"list-streamer-who-uploaded-video-dont-have-activity-in-subweek-{$date}.csv");
        }
        echo "\n Write success! \n";
    }

    public function createDailyActiveStreamers(){
        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $file_path = $dir_path . "daily-active-streamers.csv";
        if (!file_exists($file_path)){
            $end_date = Carbon::now()->subHours(8)->startOfDay()->subSeconds(1);
            $result = DB::select("select Count(Distinct user_id) as number_streamers_a, date(date_add(created_at, INTERVAL -8 HOUR )) as created_at
                              from session 
                              WHERE  date_add(created_at, INTERVAL -8 HOUR ) <= '{$end_date}'
                              GROUP BY DATE(date_add(created_at, INTERVAL -8 HOUR ))
                              ORDER BY DATE(date_add(created_at, INTERVAL -8 HOUR )) 
                            "
            );

            $number_streamer_using_boom_replay = json_decode(json_encode($result),true);

            $result = DB::select("select count(DISTINCT v.user_id) as total_streamer, count(v.id) as total_videos,sum(v.like_numb) as total_like,sum(v.view_numb) as total_view,date(date_add(v.created_at, INTERVAL -8 HOUR )) as created_at 
                                  from videos as v
                                  where date_add(v.created_at, INTERVAL -8 HOUR ) <= '{$end_date}'
                                  GROUP BY date(date_add(v.created_at, INTERVAL -8 HOUR ))  
                                  ORDER by date(date_add(v.created_at, INTERVAL -8 HOUR ))");
            $video_result = json_decode(json_encode($result),true);
            $temp_array = [];

            foreach ($video_result as $key=>$value){
                $temp_array[$value['created_at']] = $value;
            }
            $video_result = $temp_array;
            $temp_array = [];
            foreach ($number_streamer_using_boom_replay as $key=>$value){
                $number_streamer_using_boom_replay[$key]['number_active_streamers'] = $number_streamer_using_boom_replay[$key]['number_streamers_a'];
                unset($number_streamer_using_boom_replay[$key]['number_streamers_a']);

                if (isset($video_result[$value['created_at']])){
                    $number_streamer_using_boom_replay[$key]['number_streamers_who_updated_replay'] =  $video_result[$value['created_at']]['total_streamer'];
                    $number_streamer_using_boom_replay[$key]['total_videos'] = $video_result[$value['created_at']]['total_videos'];
                    $number_streamer_using_boom_replay[$key]['total_view'] = $video_result[$value['created_at']]['total_view'];
                    $number_streamer_using_boom_replay[$key]['total_like'] = $video_result[$value['created_at']]['total_like'];
                }
                else{
                    $number_streamer_using_boom_replay[$key]['number_streamers_who_updated_replay'] =  0;
                    $number_streamer_using_boom_replay[$key]['total_videos'] = 0;
                    $number_streamer_using_boom_replay[$key]['total_view'] = 0;
                    $number_streamer_using_boom_replay[$key]['total_like'] = 0;
                }
                $viewer_x = $this->getUniqueViewer($value['created_at']);
                $number_streamer_using_boom_replay[$key]['total_unique_viewers'] = $viewer_x[0];
                $number_streamer_using_boom_replay[$key]['total_viewers'] = $viewer_x[1];

            }
            $file = fopen($file_path, 'w');
            $first = 1;
            foreach ($number_streamer_using_boom_replay as $row) {
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
        }
        else{
            $start_date = Carbon::now()->subHours(8)->subDays(1)->startOfDay();
            $end_date = Carbon::now()->subHours(8)->startOfDay()->subSeconds(1);
            $result = DB::select("select Count(Distinct user_id) as number_streamers_a, date(date_add(created_at, INTERVAL -8 HOUR )) as created_at
                              from session 
                              WHERE date_add(created_at, INTERVAL -8 HOUR ) >= '{$start_date}' and date_add(created_at, INTERVAL -8 HOUR ) <= '{$end_date}' 
                              GROUP BY DATE(date_add(created_at, INTERVAL -8 HOUR ))
                              ORDER BY DATE(date_add(created_at, INTERVAL -8 HOUR )) 
                            "
            );

            $number_streamer_using_boom_replay = json_decode(json_encode($result),true);

            $result = DB::select("select count(DISTINCT v.user_id) as total_streamer, count(v.id) as total_videos,sum(v.like_numb) as total_like,sum(v.view_numb) as total_view,date(date_add(v.created_at, INTERVAL -8 HOUR )) as created_at 
                                  from videos as v
                                  where date_add(v.created_at, INTERVAL -8 HOUR ) <= '{$end_date}' AND date_add(v.created_at, INTERVAL -8 HOUR ) >= '{$start_date}'
                                  GROUP BY date(date_add(v.created_at, INTERVAL -8 HOUR ))  
                                  ORDER by date(date_add(v.created_at, INTERVAL -8 HOUR ))");
            $video_result = json_decode(json_encode($result),true);
            $temp_array = [];

            foreach ($video_result as $key=>$value){
                $temp_array[$value['created_at']] = $value;
            }
            $video_result = $temp_array;

            $temp_array = [];
            foreach ($number_streamer_using_boom_replay as $key=>$value){
                $number_streamer_using_boom_replay[$key]['number_active_streamers'] = $number_streamer_using_boom_replay[$key]['number_streamers_a'];
                unset($number_streamer_using_boom_replay[$key]['number_streamers_a']);

                if (isset($video_result[$value['created_at']])){
                    $number_streamer_using_boom_replay[$key]['number_streamers_who_updated_replay'] = $video_result[$value['created_at']]['total_streamer'];
                    $number_streamer_using_boom_replay[$key]['total_videos'] = $video_result[$value['created_at']]['total_videos'];
                    $number_streamer_using_boom_replay[$key]['total_view'] = $video_result[$value['created_at']]['total_view'];;
                    $number_streamer_using_boom_replay[$key]['total_like'] = $video_result[$value['created_at']]['total_like'];;
                }
                else{
                    $number_streamer_using_boom_replay[$key]['number_streamers_who_updated_replay'] = 0;
                    $number_streamer_using_boom_replay[$key]['total_videos'] = 0;
                    $number_streamer_using_boom_replay[$key]['total_view'] = 0;
                    $number_streamer_using_boom_replay[$key]['total_like'] = 0;
                }
                $viewer_x = $this->getUniqueViewer($value['created_at']);
                $number_streamer_using_boom_replay[$key]['total_unique_viewers'] = $viewer_x[0];
                $number_streamer_using_boom_replay[$key]['total_viewers'] = $viewer_x[1];

            }
            $file = fopen($file_path, 'a');
            $first = 1;
            foreach ($number_streamer_using_boom_replay as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }

        if (config('app.env') == 'boom-admin'){
            $link = AWSHelper::uploadReportToS3($file_path,'',"daily-active-streamers.csv");
        }
    }

    private function getUniqueViewer($date){
        $result = DB::select("SELECT vi.viewer as viewer,viewer_count
                            FROM `viewer_stream_logs` as vi, `live_streams` as li where li.id = vi.live_stream_id and date(date_add(li.stopped_time, INTERVAL -8 HOUR )) = '{$date}' and is_live = 0");
        $viewer_log = json_decode(json_encode($result),true);
        $viewer = "";
        $viewer_count = 0;
        foreach ($viewer_log as $item){
            $viewer .= " " . $item['viewer'];
            $viewer_count += $item['viewer_count'];
        }
        $viewer = trim($viewer);
        return array(floor($this->countUniqueWord($viewer) * 1.5),floor($viewer_count*1.5));
    }

    private function countUniqueWord($string){
        if ($string == ""){
            return 0;
        }
        $word_array = explode(" ",$string);
        $word_array = array_unique($word_array);
        return count($word_array);
    }
}
