<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Redis;
use App\Helpers\AWSHelper;
use Log;

class CreateCohortUserVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-cohort-user-video {cmd=current}';

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

        $start_day = Carbon::create(2017,04,12,00,00,00);
        //$start_day = Carbon::create(2017,07,01,00,00,00);
        $end_day = Carbon::now()->startOfDay();
		
        $this->start_day = Carbon::instance($start_day);
        $this->end_day = Carbon::instance($end_day)->subDays(1);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->cohortVideo();
        //$this->cohortVideoWithFollower();

    }

    public function cohortVideo(){
        $cmd = $this->argument('cmd');
        if ($cmd == 'all'){
            $start_day = Carbon::instance($this->start_day);
            $end_day = Carbon::now()->startOfDay();
            while ($start_day <= $this->end_day){
                $this->createReportByDay($start_day);
                $start_day->addDays(1);
            }
        }
        else{
            $start_day = Carbon::now()->subDays(1)->startOfDay();
            $this->createReportByDay($start_day);
        }
        $start_day = Carbon::instance($this->start_day);
        $csv_data = array();
        /*$csv_data[0] = array('Date of install');
        $csv_data[1] = array('Number of Installs');
        $csv_data[2] = array('Total streamers uploaded video');
        $csv_data[3] = array('Conversion %');*/
        $csv_data[0] = array('Date of install');
        $csv_data[1] = array('Number of Installs');
		$csv_data[2] = array('Number of Twich Installs');
		$csv_data[3] = array('Number of Mixer Installs');
        $csv_data[4] = array('Total streamers uploaded video');
		$csv_data[5] = array('Total twitch streamers uploaded video');
		$csv_data[6] = array('Total mixer streamers uploaded video');
        $csv_data[7] = array('Follow number of streamers installs');
        $csv_data[8] = array('Follow number of total streamers uploaded video');
        $csv_data[9] = array('Conversion %');
		$csv_data[10] = array('Users more than 45000 followers');
        for ($i = 0;$i < 31;$i++){
            $csv_data[$i+11] = array("First Video Uploaded Day {$i}");
        }
        while ($start_day <= $this->end_day){
            $key_cached = "boomtv:video_cohort_" . str_slug($start_day);
            $data_day = json_decode(Redis::get($key_cached), true);
            array_push($csv_data[0],$start_day->toDateString());
            array_push($csv_data[1],isset($data_day[0]) ? $data_day[0] : 0);
			array_push($csv_data[2],isset($data_day[1]) ? $data_day[1] : 0);
			array_push($csv_data[3],isset($data_day[2]) ? $data_day[2] : 0);
            array_push($csv_data[4],isset($data_day[3]) ? $data_day[3] : 0);
			array_push($csv_data[5],isset($data_day[4]) ? $data_day[4] : 0);
            array_push($csv_data[6],isset($data_day[5]) ? $data_day[5] : 0);
            array_push($csv_data[7],isset($data_day[6]) ? $data_day[6] : 0);
			array_push($csv_data[8],isset($data_day[7]) ? $data_day[7] : 0);
            $cv_percent = ($data_day[0] > 0) ? floor($data_day[3]/$data_day[0]*100) : 0;
            array_push($csv_data[9],$cv_percent . ' %');
			array_push($csv_data[10], $data_day[8]);
            for ($i = 0;$i < 31;$i++){
                if (isset($data_day[$i+10]))
                    array_push($csv_data[$i+11],$data_day[$i+9]);
                else{
                    array_push($csv_data[$i+11],0);
                }
            }

            $start_day->addDays(1);
        }

		//Log::info($csv_data);

        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $date = Carbon::now()->toDateString();
        $file_path = $dir_path . "cohort-user-video.csv";
        $file = fopen($file_path, 'w');
        foreach ($csv_data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        if (config('app.env') == 'boom-admin'){
            $link = AWSHelper::uploadReportToS3($file_path,'','cohort-user-video.csv');
        }
    }

    public function createReportByDay($day){
        $end_of_day = Carbon::instance($day);
        $end_of_day->addDay(1)->subSecond(1);
        /*$query = "select id,created_at from users WHERE is_streamer = 1 and created_at >= '{$day}' and created_at <= '{$end_of_day}'";*/
        $query = "select users.id as id,count(users.id) as number_streamer,users.created_at as created_at,SUM(social_accounts.follower_numb) as follower_numb,COUNT(CASE WHEN social_accounts.type='twitch' THEN 1 ELSE null END) as twitch_number_streamer,COUNT(CASE WHEN social_accounts.type='mixer' THEN 1 ELSE null END) as mixer_number_streamer from users JOIN social_accounts ON social_accounts.user_id = users.id WHERE is_streamer = 1 and users.created_at >= '{$day}' and users.created_at <= '{$end_of_day}'";
        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        $twitch_number_streamer = 0;
		$mixer_number_streamer = 0;
		$follow_number_install = 0;
		if(isset($results[0]))
		{
			$follow_number_install = $results[0]["follower_numb"];
			$number_streamer = $results[0]["number_streamer"];
			$twitch_number_streamer = $results[0]["twitch_number_streamer"];
			$mixer_number_streamer = $results[0]["mixer_number_streamer"];
		}
        $query = "select id from (select users.id as id, min(videos.created_at) as min_videos_created_at
                  from 
                    users,videos 
                  WHERE 
                    users.id = videos.user_id AND users.is_streamer = 1 and users.created_at >= '{$day}' and users.created_at <= '{$end_of_day}'
                  GROUP BY videos.user_id) as user_videos
                  WHERE min_videos_created_at >= '{$day}' and min_videos_created_at <= '{$end_of_day}'
               ";
        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        $first_videos_updated_day_0 = count($results);

        $query = "select userid as id,count(user_videos.userid) as total_streamer_uploaded_video,SUM(social_accounts.follower_numb) as follower_numb,COUNT(CASE WHEN social_accounts.type='twitch' THEN 1 ELSE null END) as twitch_total_streamer_uploaded_video,COUNT(CASE WHEN social_accounts.type='mixer' THEN 1 ELSE null END) as mixer_total_streamer_uploaded_video from (select users.id as userid, min(videos.created_at) as min_videos_created_at from users,videos WHERE users.id = videos.user_id AND users.is_streamer = 1 and users.created_at >= '{$day}' and users.created_at <= '{$end_of_day}' GROUP BY videos.user_id) as user_videos JOIN social_accounts ON social_accounts.user_id = user_videos.userid WHERE min_videos_created_at <= '{$end_of_day}'";
        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        $total_streamer_uploaded_video = 0;
		$twitch_total_streamer_uploaded_video = 0;
		$mixer_total_streamer_uploaded_video = 0;
        $follow_total_streamer_uploaded_video = 0;
		$users_more_than_45k_followers = '';
        //Log::info($results);
        
        if(isset($results[0]))
        {
            $total_streamer_uploaded_video = $results[0]["total_streamer_uploaded_video"];
			$twitch_total_streamer_uploaded_video = $results[0]["twitch_total_streamer_uploaded_video"];
			$mixer_total_streamer_uploaded_video = $results[0]["mixer_total_streamer_uploaded_video"];
            $follow_total_streamer_uploaded_video = $results[0]["follower_numb"];
        }

		$query = "select users.name, users.created_at, social_accounts.follower_numb from users JOIN social_accounts ON users.id = social_accounts.user_id WHERE users.is_streamer = 1 and users.created_at >= '{$day}' and users.created_at <= '{$end_of_day}' and social_accounts.follower_numb > 45000";

		$results = DB::select(DB::raw($query));
        //Log::info($results);
		
		if(!empty($results)) {
			foreach($results as $user)
				$users_more_than_45k_followers .= $user->name.',';
		}

        $key_cached = "boomtv:video_cohort_" . str_slug($day);
        Redis::set($key_cached,json_encode(array($number_streamer,$twitch_number_streamer,$mixer_number_streamer,$total_streamer_uploaded_video,$twitch_total_streamer_uploaded_video,$mixer_total_streamer_uploaded_video,$follow_number_install,
            $follow_total_streamer_uploaded_video,"$users_more_than_45k_followers",
            $first_videos_updated_day_0)));
        if ($day > $this->start_day){
            $sub_day = Carbon::instance($day)->subDays(1);
            $i = 1;
            while ($sub_day >= $this->start_day){
				$users_more_than_45k_followers = '';

                if ($i >= 30){
                    break;
                }
                $end_of_sub_day = Carbon::instance($sub_day);
                $end_of_sub_day->addDay(1)->subSecond(1);
                $query = "select id from (select users.id as id, min(videos.created_at) as min_videos_created_at
                  from 
                    users,videos 
                  WHERE 
                    users.id = videos.user_id AND users.is_streamer = 1 and users.created_at >= '{$sub_day}' and users.created_at <= '{$end_of_sub_day}'
                  GROUP BY videos.user_id) as user_videos
                  WHERE min_videos_created_at >= '{$day}' and min_videos_created_at <= '{$end_of_day}'
               ";
                $results = DB::select(DB::raw($query));
                $results = json_decode(json_encode($results), true);
                $first_videos_updated_day = count($results);

                $key_cached = "boomtv:video_cohort_" . str_slug($sub_day);
                $old_data = Redis::get($key_cached);
                $old_data = json_decode($old_data,true);
                $old_data[$i+9] = $first_videos_updated_day;

                $query = "select userid as id,count(user_videos.userid) as total_streamer_uploaded_video,SUM(social_accounts.follower_numb) as follower_numb,COUNT(CASE WHEN social_accounts.type='twitch' THEN 1 ELSE null END) as twitch_total_streamer_uploaded_video,COUNT(CASE WHEN social_accounts.type='mixer' THEN 1 ELSE null END) as mixer_total_streamer_uploaded_video from (select users.id as userid, min(videos.created_at) as min_videos_created_at from users,videos WHERE users.id = videos.user_id AND users.is_streamer = 1 and users.created_at >= '{$sub_day}' and users.created_at <= '{$end_of_sub_day}' GROUP BY videos.user_id) as user_videos JOIN social_accounts ON social_accounts.user_id = user_videos.userid WHERE min_videos_created_at <= '{$end_of_day}'";
                $results = DB::select(DB::raw($query));
                $results = json_decode(json_encode($results), true);
                //$total_streamer_uploaded_video = count($results);
				$total_streamer_uploaded_video = $results[0]['total_streamer_uploaded_video'];
				$twitch_total_streamer_uploaded_video = $results[0]["twitch_total_streamer_uploaded_video"];
				$mixer_total_streamer_uploaded_video = $results[0]["mixer_total_streamer_uploaded_video"];

				$query = "select users.name, users.created_at, social_accounts.follower_numb from users JOIN social_accounts ON users.id = social_accounts.user_id WHERE users.is_streamer = 1 and users.created_at >= '{$sub_day}' and users.created_at <= '{$end_of_sub_day}' and social_accounts.follower_numb > 45000";

				$results = DB::select(DB::raw($query));
				//Log::info($results);
				
				if(!empty($results)) {
					foreach($results as $user)
						$users_more_than_45k_followers .= $user->name.',';
				}
		
                $old_data[3] = $total_streamer_uploaded_video;
				$old_data[4] = $twitch_total_streamer_uploaded_video;
				$old_data[5] = $mixer_total_streamer_uploaded_video;
				
				$old_data[8] = $users_more_than_45k_followers;
				
                Redis::set($key_cached,json_encode($old_data));

                $sub_day->subDays(1);
                $i ++;
            }
        }

    }

    public function cohortVideoWithFollower(){
        $cmd = $this->argument('cmd');
        if ($cmd == 'all'){
            $start_day = Carbon::instance($this->start_day);
            $end_day = Carbon::now()->startOfDay();
            while ($start_day <= $this->end_day){
                $this->createReportWithFollowerByDay($start_day);
                $start_day->addDays(1);
            }
        }
        else{
            $start_day = Carbon::now()->subDays(1)->startOfDay();
            $this->createReportWithFollowerByDay($start_day);
        }

        $start_day = Carbon::instance($this->start_day);
        $csv_data = array();
        $csv_data[0] = array('Date of install');
        $csv_data[1] = array('Number of Installs');
        $csv_data[2] = array('Total number of followers');
        while ($start_day <= $this->end_day){
            $key_cached = "boomtv:video_cohort_with_follow" . str_slug($start_day);
            $data_day = json_decode(Redis::get($key_cached));
            array_push($csv_data[0],$start_day->toDateString());
            array_push($csv_data[1],isset($data_day[0]) ? $data_day[0] : 0);
            array_push($csv_data[2],isset($data_day[1]) ? $data_day[1] : 0);
            $start_day->addDays(1);
        }


        $dir_path = storage_path('app/admin-report/');
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        $date = Carbon::now()->toDateString();
        $file_path = $dir_path . "cohort-user-video-with-followers.csv";
        $file = fopen($file_path, 'w');
        foreach ($csv_data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        if (config('app.env') == 'boom-admin'){
            $link = AWSHelper::uploadReportToS3($file_path,'','cohort-user-video-with-followers.csv');
        }
    }

    public function createReportWithFollowerByDay($day){
        $end_of_day = Carbon::instance($day);
        $end_of_day->addDay(1)->subSecond(1);
        $query = "select id,created_at from users WHERE is_streamer = 1 and created_at >= '{$day}' and created_at <= '{$end_of_day}'";
        $results = DB::select(DB::raw($query));
        $results = json_decode(json_encode($results), true);
        $number_streamer = count($results);

        $query = "select sum(sc.follower_numb) as total_follow_numb
                    from social_accounts as sc, users as u
                    where sc.user_id = u.id and u.created_at >= '{$day}' and u.created_at <= '{$end_of_day}' and u.is_streamer = 1
               ";
        $results = DB::select(DB::raw($query));
        $total_follower = $results[0]->total_follow_numb;

        $key_cached = "boomtv:video_cohort_with_follow" . str_slug($day);
        Redis::set($key_cached,json_encode(array($number_streamer,$total_follower)));

    }
}