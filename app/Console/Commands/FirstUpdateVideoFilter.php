<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Video;
use App\Models\VideoGame;
use App\Models\ViewDay;

class FirstUpdateVideoFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:first-update-video-filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'First update video filter';

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
        Video::first_update_view_week();
        Video::markFilterType();
        VideoGame::updateCategoryGames();
        ViewDay::clearViewDay();
    }
}
