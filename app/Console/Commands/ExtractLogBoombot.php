<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

use App\Helpers\AWSHelper;

class ExtractLogBoombot extends Command
{
    use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:extract_log_boombot {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract log boombot with arg start_date and end_date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

       // $this->setMutexStrategy('redis');

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(config('app.env') == 'local' || config('app.env') == 'extract_log_boombot')
        {
            $bucket = config("aws.bucket_boombot");
            $folder = config("aws.folder_log_boombot");
            $startDate = $this->argument("start_date");
            $startDate = trim($startDate);
            $endDate = $this->argument("end_date");
            $endDate = trim($endDate);
            AWSHelper::getFilesInFolderForTime($bucket, $folder, $startDate, $endDate);
        }
    }
}
