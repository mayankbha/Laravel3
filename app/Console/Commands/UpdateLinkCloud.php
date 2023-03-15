<?php

namespace App\Console\Commands;
use App\Models\Video;
use Illuminate\Console\Command;

class UpdateLinkCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:update-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update link cloudfront for video';

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

        $data=Video::select('id','thumbnail','links3')->get();
        foreach ($data as $key => $value) {
            $links3=str_replace("https://s3-us-west-2.amazonaws.com/afkvr-videos", "", $value->links3);
            $thumbnail=str_replace("https://s3-us-west-2.amazonaws.com/afkvr-videos", "", $value->thumbnail);
            Video::where('id',$value->id)->update([
                    'thumbnail'=>$thumbnail,
                    'links3'=>$links3
                ]);
        }
        $this->info('Update link cloudfront successfully');
    }
}
