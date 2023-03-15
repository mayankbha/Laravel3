<?php

namespace App\Console\Commands;
use App\Models\Video;
use Illuminate\Console\Command;
use App\Helpers\AWSHelper;
class ConvertOldVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:convert-video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert old videos from mp4 to hls';

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
        $data=Video::where('game_id',9)->where('job_id','')->where('link_hls','')->where('hls_type','<>',3)->get();
        foreach ($data as $key => $value) {
            $job_id=AWSHelper::convertHLS_360($value->links3);
            Video::where('id',$value->id)->update([
                "job_id"=>$job_id,
                "hls_type" => 3,
                "link_hls"=>'/videos-hls/'.str_replace("/videos/","", $value->links3).'.m3u8'
            ]);
        }
        $this->info('Converted '.count($data).' videos successfully');
    }
}
