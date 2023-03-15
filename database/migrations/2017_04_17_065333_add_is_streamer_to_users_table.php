<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;

class AddIsStreamerToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_streamer')->nullable()->default(0);
        });
        $users = User::all();
        foreach ($users as $key => $user) 
        {
            if($user->is_claim == 0 || $user->source == User::SOURCE_APP)
            {
                $user->source = User::SOURCE_APP;
                $user->is_streamer = 1;
            }
            else
            {
                $user->source = User::SOURCE_WEB;
            }
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_streamer');
        });
    }
}
