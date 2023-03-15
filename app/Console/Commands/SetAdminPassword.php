<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class SetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:set-admin-password {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set admin password from argv';

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
        $new_pass = $this->argument("password");
        $new_pass = trim($new_pass);
        $admin_id = 1;
        if ($new_pass == ""){
            return "Password null";
        }
        else{
            Admin::where('id',1)->update(['password'=>bcrypt($new_pass)]);
            echo "\nChange password success " . $new_pass . "\n";
            return true;
        }
    }
}
