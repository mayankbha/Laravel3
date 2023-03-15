<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserAdminPermission;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class AdminUtil extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boomtv:admin-util {cmd=list-permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin permission utility';

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
            case "search" :
                $this->search_user();
                break;
            case "add-permission" :
                $this->add_permission();
                break;
            case "remove-permission" :
                $this->remove_permission();
                break;
            case "list-permission" :
                $this->list_permission();
                break;
        }
    }

    public function list_permission(){
        $output = new ConsoleOutput();

        $list_user_permission = UserAdminPermission::all();

        if ($list_user_permission->count()){
            foreach ($list_user_permission as $user){
                $output->writeln(json_encode($user));
            }
        }
        else{
            $output->writeln('No permission match.');
        }
    }

    public function search_user(){
        $output = new ConsoleOutput();

        $key = $this->ask("Name ?");
        $list_user = User::where('name','like',"%{$key}%")->get();

        if ($list_user->count()){
            foreach ($list_user as $user){
                $output->writeln(json_encode($user));
            }
        }
        else{
            $output->writeln('No user match.');
        }
    }

    public function add_permission(){
        $output = new ConsoleOutput();

        $id = $this->ask("User id ?");

        $user_object = User::where('id',$id)->first();
        $permission_object = UserAdminPermission::where('user_id',$id)->first();
        if ($permission_object){
            $output->writeln("Permission exists");
            return ;
        }
        if (!$user_object){
            $output->writeln("User not found");
        }
        else{
            $permission =  new UserAdminPermission();
            $permission->user_id = $id;
            $permission->permission = "all";
            $permission->save();
            $output->writeln("Success");
        }

    }

    public function remove_permission(){
        $output = new ConsoleOutput();

        $id = $this->ask("User id ?");

        $user_object = User::where('id',$id)->first();
        $permission_object = UserAdminPermission::where('user_id',$id)->first();

        if ($permission_object){
            $permission_object->delete();
            $output->writeln("Success");
        }
        else{
            $output->writeln("Permission not found");
        }

    }
}
