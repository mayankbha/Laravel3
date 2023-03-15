<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Redis;
use App\Helpers\AWSHelper;
use Log;

use Ixudra\Curl\Facades\Curl;

class CreateTwitchUser extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
protected $signature = 'boomtv:create-twitch-user {path="D:\wamp\www\boomtv\storage\twitch\EMAILS - 08-02 04-38-18.csv"}';

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
		$this->cohortVideo();
	}

	public function cohortVideo() {
		$cmd = $this->argument('path');

		if($cmd == '')
			$twitch_user_csv_dir_path = storage_path('twitch/EMAILS - 08-02 04-38-18.csv');

		$twitch_user_csv_dir_path = $cmd;

		$dir_path = storage_path('twitch/');

		if(!is_dir($dir_path)) {
			mkdir($dir_path, 0755, true);
		}

		$file_path = $dir_path . "out.csv";

		$file = fopen($twitch_user_csv_dir_path, 'r') or die ("could not open the file!");

		$twitch_user = array();

		$wfile = fopen($file_path, 'w');

		while(($line = fgetcsv($file)) !== FALSE) {
			//$line is an array of the csv elements
			$username = $line[4];
			$email = $line[16];

			if($email != '')
				$check_user_exist = DB::select("select Count(*) as cnt from users WHERE email = '$email'");
			else
				$check_user_exist = DB::select("select Count(*) as cnt from users WHERE name = '$username'");

			//echo "<pre>"; print_r($check_user_exist);

			if($check_user_exist[0]->cnt == 0)
				fputcsv($wfile, $line);

			//Log::info($line);
		}

		fclose($file);

		fclose($wfile);

		if(config('app.env') == 'boom-admin') {
			//$link = AWSHelper::uploadReportToS3($file_path, '', 'our.csv');
		}
	}
}