<?php

namespace App\Jobs;

use Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class CachedUpdated extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $action;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($action,$data = [])
    {
        //
        $this->action = $action;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if ($this->action == 'recent'){
            Log::info("Reset recent cached");
            Artisan::call('boomtv:create-cached',['cmd'=>'recent']);
        }
        elseif ($this->action == 'highlight'){
            Log::info("Reset highlight cached");
            Artisan::call('boomtv:create-cached',['cmd'=>'highlight']);
        }
        elseif($this->action == "game"){
            Log::info("Reset video game cached");
            Artisan::call('boomtv:create-cached',['cmd'=>'game','game'=>$this->data['game_id']]);
        }
    }
}
