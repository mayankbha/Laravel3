<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class VideoUtil extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:video-util {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Video utility';

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

        $cmd = $this->argument('cmd');
        switch ($cmd){
            case 'remove' :
                $this->remove_video();
                break;
            case 'undo' :
                $this->un_remove();
            default :
                break;
        }

    }

    public function remove_video(){
        $code = $this->ask('Video code : ?');
        $video = Video::where('code',$code)->first();
        if ($video){
            $video->status = 3;
            $video->save();
        }
        $this->info("Success");
    }

    public function un_remove(){
        $code = $this->ask('Video code : ?');
        $video = Video::where('code',$code)->first();
        if ($video){
            $video->status = 1;
            $video->save();
        }
        $this->info("Success");
    }
}
