<?php

namespace App\Console\Commands;
use App\Models\Video;
use App\Models\VideoGame;
use App\Models\ViewDay;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class UpdateVideoFilter extends Command
{
    use WithoutOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'boomtv:update-video-filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMutexStrategy("redis");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Video::markFilterType();
        VideoGame::updateCategoryGames();
        ViewDay::clearViewDay();
    }
}
