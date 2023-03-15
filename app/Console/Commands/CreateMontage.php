<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

use App\Models\Video;

class CreateMontage extends Command
{
    use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:create-montage';

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

       // $this->setMutexStrategy('redis');

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        die();
        if(config('app.env') == 'local' || config('app.env') == 'create_montage')
        {
            Video::createMontage();
        }
    }
}
