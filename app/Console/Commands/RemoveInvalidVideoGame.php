<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\VideoGame;
use App\Models\Video;

class RemoveInvalidVideoGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:remove-invalid-video-game';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove video game relation if video does not exist';

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
        VideoGame::remove_invalid_video_game();
    }
}
