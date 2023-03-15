<?php

namespace App\Console\Commands;
use App\Models\Video;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;

class UpdateUserProfile extends Command
{
    use WithoutOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'boomtv:update-profile';

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
        SocialAccount::updateProfile();
        SocialAccount::updateProfileMixer();
        SocialAccount::updateProfileYoutube();
    }
}
