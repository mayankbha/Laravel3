<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AWSHelper;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Lang;
use App\Models\User;
use App\Models\Video;
use DB;
use Carbon\Carbon;
class AdminController extends Controller
{
    //

    protected $guard = "admin";

    public function index(Request $request){
        $report_data = [];

        $result = DB::table('users')->select(DB::raw('count(users.id) as user_count'))->where('is_streamer',1)->get();
        $report_data['total_streamer_who_installed'] = isset($result[0]) ? $result[0]->user_count : 0;
        $result = DB::table('users')->select(DB::raw('count(users.id) as user_count'))->where('is_streamer',1)->where('created_at','>=','2017-04-12 00:00:00')->get();
        $report_data['total_streamer_who_installed_rel'] = isset($result[0]) ? $result[0]->user_count : 0;
        $result = DB::select("SELECT COUNT( u.id ) AS user_count
                            FROM (
                            SELECT users.id AS id,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS v_count
                            FROM users, videos
                            WHERE videos.user_id = users.id
                            GROUP BY users.id
                            ) AS u
                            WHERE u.v_count > 0 AND u.is_streamer = 1
                            "
        );
        $report_data['total_streamer_who_uploaded_video'] = isset($result[0]) ? $result[0]->user_count : 0;
        $result = DB::select("SELECT COUNT( u.id ) AS user_count
                            FROM (
                            SELECT users.id AS id,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS v_count
                            FROM users, videos
                            WHERE videos.user_id = users.id
                            GROUP BY videos.user_id
                            ) AS u
                            WHERE u.created_at > '2017-04-12 00:00:00'
                            AND u.v_count > 0 AND u.is_streamer = 1
                            "
        );
        $report_data['total_streamer_who_uploaded_video_rel'] = isset($result[0]) ? $result[0]->user_count : 0;
        $sub_week = Carbon::now()->subWeek();
        $result = DB::select("SELECT count(u.id) as user_count FROM ( SELECT users.id AS id,users.name as name,users.email as email,users.is_streamer as is_streamer, users.created_at AS created_at, COUNT( videos.id ) AS video_count,max(videos.created_at) as lattest_video_created FROM users, videos WHERE videos.user_id = users.id GROUP BY users.id ) AS u WHERE u.video_count >= 2 AND u.is_streamer = 1 and lattest_video_created < '{$sub_week}' ORDER BY u.id DESC ");
        $report_data['total_streamer_who_uploaded_video_dont_have_activity_in_subweek'] = isset($result[0]) ? $result[0]->user_count : 0;

        $streamer_no_activity = array();
        $dir_path = storage_path('app/admin-report/');
        $date =  Carbon::now()->subHours(12)->toDateString();

        $file_name = 'app/admin-report/' . "list-streamer-who-uploaded-video-dont-have-activity-in-subweek-{$date}.csv";
        $csv_file_name = storage_path($file_name);


        if (config('app.env') == 'production'){
            $report_data['streamer_no_activity'] = array('storage' => 's3','file_name' => config('aws.linkS3Report') . "list-streamer-who-uploaded-video-dont-have-activity-in-subweek-{$date}.csv" );
            $obj = AWSHelper::getObjectFromAdminReportS3("list-streamer-who-uploaded-video-dont-have-activity-in-subweek-{$date}.csv");
            if ($obj){
                $report_data['streamer_no_activity']['date'] = $obj['LastModified']->format("Y-m-d H:i:s");
            }
            else{
                $report_data['streamer_no_activity']['date'] = "";
            }
        }
        else{
            $report_data['streamer_no_activity'] = array('storage' => 'local','file_name' => $file_name);
        }

        $file_name = 'app/admin-report/' . "cohort-user-video.csv";
        $csv_file_name = storage_path($file_name);
        if (config('app.env') == 'production'){
            $report_data['cohort_user_video'] = array('storage' => 's3','file_name' => config('aws.linkS3Report') . 'cohort-user-video.csv');
            $obj = AWSHelper::getObjectFromAdminReportS3("cohort-user-video.csv");
            if ($obj){
                $report_data['cohort_user_video']['date'] = $obj['LastModified']->format("Y-m-d H:i:s");
            }
            else{
                $report_data['cohort_user_video']['date'] = "";
            }
        }
        else{
            $report_data['cohort_user_video'] = array('storage' => 'local','file_name' => $file_name);
        }

        $file_name = 'app/admin-report/' . "daily-active-streamers.csv";
        $csv_file_name = storage_path($file_name);
        if (config('app.env') == 'production'){
            $report_data['daily_active_streamers'] = array('storage' => 's3','file_name' => config('aws.linkS3Report') . 'daily-active-streamers.csv');
            $obj = AWSHelper::getObjectFromAdminReportS3("daily-active-streamers.csv");
            if ($obj){
                $report_data['daily_active_streamers']['date'] = $obj['LastModified']->format("Y-m-d H:i:s");
            }
            else{
                $report_data['daily_active_streamers']['date'] = "";
            }
        }
        else{
            $report_data['daily_active_streamers'] = array('storage' => 'local','file_name' => $file_name);
        }

        $week_string = Carbon::now()->subHours(12)->startOfWeek()->toDateString();
        $file_name = 'app/admin-report/' . "weekly-active-streamers-{$week_string}.csv";;
        if (config('app.env') == 'production'){
            $report_data['weekly_active_streamers'] = array('storage' => 's3','file_name' => config('aws.linkS3Report') .  "weekly-active-streamers-{$week_string}.csv");
            $obj = AWSHelper::getObjectFromAdminReportS3("weekly-active-streamers-{$week_string}.csv");
            if ($obj){
                $report_data['weekly_active_streamers']['date'] = $obj['LastModified']->format("Y-m-d H:i:s");
            }
            else{
                $report_data['weekly_active_streamers']['date'] = "";
            }
        }
        else{
            $report_data['weekly_active_streamers'] = array('storage' => 'local','file_name' => $file_name);
        }


        $result = DB::select("select sum(view_numb) as total_view from videos where status = 1");
        $report_data['total_view'] = isset($result[0]) ? $result[0]->total_view : 0;

        $result = DB::select("select sum(like_numb) as total_like from videos where status = 1");
        $report_data['total_like'] = isset($result[0]) ? $result[0]->total_like : 0;

        $result = DB::select("select count(id) as total_videos from videos where status = 1");
        $report_data['total_videos'] = isset($result[0]) ? $result[0]->total_videos : 0;

        $last_day_start = Carbon::now()->subHours(8)->subDays(1)->startOfDay();
        $last_day_end = Carbon::now()->subHour(8)->startOfDay()->subSeconds(1);
        $result = DB::select("select Count(Distinct user_id) as number_streamers_a
                              from session 
                              WHERE date_add(created_at, INTERVAL -8 HOUR ) >= '{$last_day_start}' and date_add(created_at, INTERVAL -8 HOUR ) <= '{$last_day_end}'
                            "
        );

        $report_data['number_streamers_using_boom_last_day'] = isset($result[0]) ? $result[0]->number_streamers_a : 0;

        $result = DB::select("select count(DISTINCT v.user_id) number_streamers_b from videos as v
                              WHERE date_add(v.created_at, INTERVAL -8 HOUR ) >= '{$last_day_start}' AND date_add(v.created_at, INTERVAL -8 HOUR ) < '{$last_day_end}'
                            "
        );

        $report_data['number_streamers_has_replay_last_day'] = isset($result[0]) ? $result[0]->number_streamers_b : 0;

        return view('admin.home.index',['report_data'=>$report_data]);
    }

    public function csvViewer(Request $request){
        $file_name = $request->input('file');
        if (!isset($file_name)){
            return abort(403);
        }
        $file = storage_path($file_name);
        $file = fopen($file,'r');
        $data = [];
        while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            $data[] = $line;
        }
        fclose($file);
        return view('admin.home.csv-viewer',['data'=>$data,'file_name'=>$file_name]);

    }

    public function csvDownload(Request $request){
        $file_name = $request->input('file');
        if (!isset($file_name)){
            return abort(403);
        }
        $file = storage_path($file_name);
        return response()->download($file);
    }

    public function showSetting(Request $request){
        $settings = Setting::where('scope','all')->get();

        $msg = $request->session()->pull('setting_msg');

        return view('admin.setting.index', [
            'settings'=>$settings,
            'setting_msg' => $msg,
        ]);
    }

    public function saveSetting(Request $request){
        $name_array = $request->input('name');
        $value_array = $request->input('value');

        foreach ($name_array as $key=>$item){
            $setting = Setting::where('name',$item)->first();
            $setting->value = $value_array[$key];
            $setting->save();
        }

        $request->session()->push('setting_msg.msg',Lang::get('setting.success_update_setting'));
        $request->session()->push('setting_msg.status','success');
        return redirect()->route('admin.setting');
    }

    public function dailyActiveStreamers(Request $request){

        $data = [];

        $global_start_date_tmp = DB::select("select min(created_at) as min_created_at 
                              from session 
                              WHERE 1
                            "
        );
        $global_start_date = isset($global_start_date_tmp[0]) ? Carbon::instance( new \DateTime($global_start_date_tmp[0]->min_created_at)) : Carbon::now();
        $global_start_date->startOfMonth();
        $all_month = array();
        while ($global_start_date <=  Carbon::now()){
            $all_month[] = $global_start_date->format("Y-m");
            $global_start_date->addMonths(1);
        }
        $data['all_month'] = $all_month;

        $month_select = $request->input('month');
        if ($month_select ==  null){
            $month_select = Carbon::now()->format('Y-m');
        }
        elseif(!$this->validate($request,[
            'month' => 'regex:/^(\d+)\-(\d+)$/'
        ])){
            $month_select = Carbon::now()->format('Y-m');
        }



        $date_month_select = Carbon::instance(new \DateTime($month_select . "-1 00:00:00"));

        $start_date = Carbon::instance($date_month_select)->startOfMonth();
        $end_date = Carbon::instance($date_month_select)->addMonth()->startOfMonth()->subSeconds(1);

        $result = DB::select("select Count(Distinct user_id) as number_streamers_a, date(created_at) as created_at
                              from session 
                              WHERE created_at >= '{$start_date}' and created_at <= '{$end_date}' 
                              GROUP BY DATE(created_at)
                              ORDER BY DATE(created_at) 
                            "
        );

        $number_streamer_using_boom_replay = json_decode(json_encode($result),true);


        $result = DB::select("select Count(Distinct s.user_id) as number_streamers_b, date(s.created_at) as created_at from session as s,videos as v 
                              WHERE s.user_id = v.user_id and date(s.created_at) = date(v.created_at)  and s.created_at > '{$start_date}' and s.created_at < '{$end_date}'
                              GROUP BY DATE(s.created_at) ORDER BY DATE(s.created_at) 
                            "
        );
        $number_streamer_has_boom_replay = json_decode(json_encode($result),true);

        $temp_array = [];
        foreach ($number_streamer_has_boom_replay as $key=>$value){
            $temp_array[$value['created_at']] = $value;
        }
        $number_streamer_has_boom_replay = $temp_array;

        $temp_array = [];
        foreach ($number_streamer_using_boom_replay as $key=>$value){
            if (isset($number_streamer_has_boom_replay[$value['created_at']])){
                $number_streamer_using_boom_replay[$key]['number_streamers_b'] = $number_streamer_has_boom_replay[$value['created_at']]['number_streamers_b'];
            }
            else{
                $number_streamer_using_boom_replay[$key]['number_streamers_b'] = 0;
            }
        }

        $data['streamer_data'] = $number_streamer_using_boom_replay;


        return view('admin.home.daily-active-streamers',['data'=>$data]);
    }

    public function getUploadDiffTool(Request $request){
        return view("admin.home.csv-difftool");
    }

    public function postUploadDiffTool(Request $request){
        $file = $request->hasFile('file') ? $request->file("file") : null;

        if ($file == null){
            return view("admin.home.csv-difftool",['msg'=>"File not found"]);
        }
        if ($file->getClientMimeType() != "text/csv"){
            return view("admin.home.csv-difftool",['msg'=>"Client mime type must text/csv"]);
        }

        $list_streamer = User::where('is_streamer',1)->get();
        $list_streamer_1 = $list_streamer->keyBy('email');

        $list_streamer_2 = $list_streamer->keyBy('name');

        $file_user_sub = $file->getPathname();
        $row = 1;
        $data_key = array();
        $first = 1;
        $user_sub_data = array();
        if (($handle = fopen($file_user_sub, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($first){
                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        $data_key[$c] = $data[$c];
                    }
                    $first = 0;
                }
                else{
                    $num = count($data);
                    $value = array();
                    for ($c=0; $c < $num; $c++) {
                        $value[$data_key[$c]] = $data[$c];
                    }
                    if (!filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                        if (!$list_streamer_2->get($data[0])){
                            $user_sub_data[] = $value;
                        }
                    }
                    else{
                        if (!$list_streamer_1->get($data[0])){
                            $user_sub_data[] = $value;
                        }
                    }
                }

            }
            fclose($handle);
        }
        $user_id = auth()->guard('admin')->id();
        $dir_path = storage_path("app/admin-diff-tool/{$user_id}");
        if (!is_dir($dir_path)){
            mkdir($dir_path,0755,true);
        }
        $current_time = Carbon::now()->toDateTimeString();
        $file_path =$dir_path . "/" . "sub_members_subtract_streamer-{$current_time}.csv";
        $file = fopen($file_path, 'w');
        $first = 1;
        foreach ($user_sub_data as $row) {
            if ($first){
                $array_key = [];
                foreach ($row as $key=>$value){
                    $array_key[] = $key;
                }
                fputcsv($file, $array_key);
                $first = 0;
            }
            fputcsv($file, $row);
        }
        fclose($file);
        return response()->download($file_path);
    }
}