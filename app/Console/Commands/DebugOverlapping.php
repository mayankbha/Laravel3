<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class DebugOverlapping extends Command
{
    use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:debug-overlapping';

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
        $this->setMutexStrategy("redis");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $data = Carbon::now() . " : " . " running";
        $content = Cache::get('test:debug-overlapping',"Start");
        $content = $content . " | " . $data;
        Cache::put('test:debug-overlapping',$content,30);
    }
}
