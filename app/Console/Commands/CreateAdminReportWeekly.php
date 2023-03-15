<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Mail;
use Log;
use App\Helpers\AWSHelper;


class CreateAdminReportWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-admin-report-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin report weekly';

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
        //$this->createWeeklyActiveStreamers();
        $this->createWeeklyStreamersMore100Sub();
    }


    private function createWeeklyStreamersMore100Sub()
    {
        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $week_string = Carbon::now()->startOfWeek()->toDateString();
        $file_path = $dir_path . "weekly-active-streamers-{$week_string}.csv";
        $query = "select 
                    us.name,us.email,us.subscriber_numb,us.created_at as streamer_created_at ,v.last_video_created_at,v.number_video as number_videos
                  from 
                    (SELECT u.name as name,u.email as email,u.id as id,sc.subscriber_numb as subscriber_numb,u.created_at as created_at FROM `users` as u,social_accounts as sc where u.id = sc.user_id and sc.subscriber_numb > 0 order by sc.subscriber_numb desc) as us left join 
                    (select user_id as user_id,count(id) as number_video,max(created_at) as last_video_created_at from videos where 1 group by user_id order by id desc ) as v 
                  on v.user_id = us.id 
                  where us.subscriber_numb >= 100
                  group by us.id 
                  order by us.subscriber_numb desc";
        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
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
        if (config('app.env') == 'boom-admin') {
            $link = AWSHelper::uploadReportToS3($file_path, '', "weekly-active-streamers-{$week_string}.csv");
        }
    }

    private function createWeeklyActiveStreamers()
    {
        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $file_path = $dir_path . "weekly-active-streamers.csv";
        if (/*!file_exists($file_path)*/
        1
        ) {
            $end_date = Carbon::now()->startOfWeek()->subSeconds(1);
            $result = DB::select("select count(Distinct user_id) as number_streamers_a, date(DATE_ADD(created_at, INTERVAL(-WEEKDAY(created_at)) DAY)) as first_week_day
                              from session 
                              WHERE  created_at <= '{$end_date}'
                              GROUP BY week(created_at,1)
                              ORDER BY first_week_day
                            "
            );

            $number_streamer_using_boom_replay = json_decode(json_encode($result), true);
            $temp_array = [];
            foreach ($number_streamer_using_boom_replay as $key => $value) {
                $temp_array[$value['first_week_day']] = $value;
            }
            $number_streamer_using_boom_replay = $temp_array;

            $result = DB::select("select count(Distinct s.id) as number_streamers_b, date(DATE_ADD(v.created_at, INTERVAL(-WEEKDAY(v.created_at)) DAY)) as first_week_day from users as s,videos as v 
                              WHERE s.id = v.user_id and s.created_at <= '{$end_date}'
                              GROUP BY week(v.created_at,1) ORDER BY first_week_day
                            "
            );
            $number_streamer_has_boom_replay = json_decode(json_encode($result), true);

            $temp_array = [];
            foreach ($number_streamer_has_boom_replay as $key => $value) {
                $temp_array[$value['first_week_day']] = $value;
            }
            $number_streamer_has_boom_replay = $temp_array;

            $result = DB::select("select date(DATE_ADD(created_at, INTERVAL(-WEEKDAY(created_at)) DAY)) as first_week_day,count(v.id) as total_videos,sum(v.like_numb) as total_like,sum(v.view_numb) as total_view 
                                  from videos as v
                                  where v.status = 1 and created_at >= '2017-03-06 00:00:00' and created_at <= '{$end_date}'
                                  GROUP BY week(v.created_at,1)  
                                  ORDER by first_week_day
                                  ");
            $video_result = json_decode(json_encode($result), true);
            $temp_array = [];

            foreach ($video_result as $key => $value) {
                $temp_array[$value['first_week_day']] = $value;
            }
            $video_result = $temp_array;
            $temp_array = [];
            foreach ($video_result as $key => $value) {
                if (isset($number_streamer_using_boom_replay[$value['first_week_day']])) {
                    $video_result[$key]['number_streamer_using_boom_replay'] = $number_streamer_using_boom_replay[$value['first_week_day']]['number_streamers_a'];
                } else {
                    $video_result[$key]['number_streamer_using_boom_replay'] = 0;
                }
                if (isset($number_streamer_has_boom_replay[$value['first_week_day']])) {
                    $video_result[$key]['number_streamers_who_uploaded_replay'] = $number_streamer_has_boom_replay[$value['first_week_day']]['number_streamers_b'];
                } else {
                    $video_result[$key]['number_streamers_who_uploaded_replay'] = 0;
                }


                $viewer_x = $this->getUniqueViewer($value['first_week_day']);
                $video_result[$key]['total_unique_viewers'] = $viewer_x[0];
                $video_result[$key]['total_viewers'] = $viewer_x[1];

            }
            $file = fopen($file_path, 'w');
            $first = 1;
            foreach ($video_result as $row) {
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
        } else {
            $end_date = Carbon::now()->startOfWeek();
            $start_date = Carbon::instance($end_date)->subDays(7);
            $end_date->subSeconds(1);

            $result = DB::select("select Count(Distinct user_id) as number_streamers_a, date(DATE_ADD(created_at, INTERVAL(-WEEKDAY(created_at)) DAY)) as first_week_day
                              from session 
                              WHERE  created_at <= '{$end_date}' and created_at >= '{$start_date}'
                              GROUP BY week(created_at,1)
                              ORDER BY first_week_day
                            "
            );

            $number_streamer_using_boom_replay = json_decode(json_encode($result), true);
            $temp_array = [];
            foreach ($number_streamer_using_boom_replay as $key => $value) {
                $temp_array[$value['first_week_day']] = $value;
            }
            $number_streamer_using_boom_replay = $temp_array;

            $result = DB::select("select Count(Distinct v.user_id) as number_streamers_b, date(DATE_ADD(u.created_at, INTERVAL(-WEEKDAY(u.created_at)) DAY)) as first_week_day from user as u,videos as v 
                              WHERE s.user_id = v.user_id and week(s.created_at,1) = week(v.created_at,1) and s.created_at <= '{$end_date}' and s.created_at >= '{$start_date}'
                              GROUP BY week(s.created_at,1) ORDER BY first_week_day
                            "
            );
            $number_streamer_has_boom_replay = json_decode(json_encode($result), true);

            $temp_array = [];
            foreach ($number_streamer_has_boom_replay as $key => $value) {
                $temp_array[$value['first_week_day']] = $value;
            }
            $number_streamer_has_boom_replay = $temp_array;

            $result = DB::select("select date(DATE_ADD(created_at, INTERVAL(-WEEKDAY(created_at)) DAY)) as first_week_day,count(v.id) as total_videos,sum(v.like_numb) as total_like,sum(v.view_numb) as total_view 
                                  from videos as v
                                  where v.status = 1 and created_at >= '{$start_date}' and created_at <= '{$end_date}'
                                  GROUP BY week(v.created_at,1)  
                                  ORDER by first_week_day
                                  ");
            $video_result = json_decode(json_encode($result), true);
            $temp_array = [];

            foreach ($video_result as $key => $value) {
                $temp_array[$value['first_week_day']] = $value;
            }
            $video_result = $temp_array;
            $temp_array = [];
            foreach ($video_result as $key => $value) {
                if (isset($number_streamer_using_boom_replay[$value['first_week_day']])) {
                    $video_result[$key]['number_streamer_using_boom_replay'] = $number_streamer_using_boom_replay[$value['first_week_day']]['number_streamers_a'];
                } else {
                    $video_result[$key]['number_streamer_using_boom_replay'] = 0;
                }
                if (isset($number_streamer_has_boom_replay[$value['first_week_day']])) {
                    $video_result[$key]['number_streamers_who_updated_replay'] = $number_streamer_has_boom_replay[$value['first_week_day']]['number_streamers_b'];
                } else {
                    $video_result[$key]['number_streamers_who_updated_replay'] = 0;
                }


                $viewer_x = $this->getUniqueViewer($value['first_week_day']);
                $video_result[$key]['total_unique_viewers'] = $viewer_x[0];
                $video_result[$key]['total_viewers'] = $viewer_x[1];

            }
            $file = fopen($file_path, 'a+');
            $first = 1;
            foreach ($video_result as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }

        if (config('app.env') == 'boom-admin') {
            $link = AWSHelper::uploadReportToS3($file_path, '', "weekly-active-streamers.csv");
        }
    }

    private function getUniqueViewer($date)
    {
        $datetime = new \DateTime($date);
        $datetime = Carbon::instance($datetime);
        $end_of_week = $datetime->addDays(7)->subSeconds(1)->toDateString();
        $result = DB::select("SELECT vi.viewer as viewer,viewer_count
                            FROM `viewer_stream_logs` as vi, `live_streams` as li where li.id = vi.live_stream_id and date(li.created_at) >= '{$date}' and date(li.created_at) <= '{$end_of_week}' and is_live = 0");
        $viewer_log = json_decode(json_encode($result), true);
        $viewer = "";
        $viewer_count = 0;
        foreach ($viewer_log as $item) {
            $viewer .= " " . $item['viewer'];
            $viewer_count += $item['viewer_count'];
        }
        $viewer = trim($viewer);
        return array($this->countUniqueWord($viewer), $viewer_count);
    }

    private function countUniqueWord($string)
    {
        if ($string == "") {
            return 0;
        }
        $word_array = explode(" ", $string);
        $word_array = array_unique($word_array);
        return count($word_array);
    }
}
