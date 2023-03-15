<?php

namespace App\Console\Commands;
use App\Models\Video;
use Illuminate\Console\Command;
use App\Helpers\AWSHelper;
use Log;
class UpdateResolution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:update-resolution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update resolution for hls video';

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
        $data=Video::where('type','!=',2)->get();
        foreach ($data as $key => $value) {
            try
            {
                $job_id=AWSHelper::convertHLS_3D($value->links3, true, $value->id);
                Video::where('id',$value->id)->update([
                    "job_id"=>$job_id,
                    "hls_type" => 3,
                    "link_hls"=>'/videos-hls/'.str_replace("/videos/","", $value->links3. $value->id).'.m3u8'
                ]);
                $this->info('Update resolution success for video id: ' . $value->id);
            } 
            catch(\Exception $e)
            {

                $this->error('Something went wrong!');
                Log::error("Update resolution error for video id: " . $value->id); 
            }
        }
        $this->info('Converted '.count($data).' videos successfully');
    }
}
