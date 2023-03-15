<?php

namespace App\Console\Commands;
use App\Models\Video;
use Illuminate\Console\Command;

class FixThubnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'boomtv:fix-thumbnail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix thumbnail video 360';

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
        $data=Video::where('type',2)->get();
        foreach ($data as $key => $value) {
            $thumbnail="/temp".str_replace("/temp", "", $value->thumbnail);
            Video::where('id',$value->id)->update([
                    'thumbnail'=>$thumbnail,
                ]);
        }
        $this->info('Fix link thumbnail successfully');
    }
}
