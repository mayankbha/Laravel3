<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\TwitchOauth;
use App\Models\Token;


class CreateTwitchoauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitchoauth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('access_token_tw');
            $table->string('refresh_token_tw');
            $table->string('expires_in_tw')->nullable();
            $table->timestamps();
        });

        $userAll = User::all();
        foreach ($userAll as $user) 
        {
            $twitch = TwitchOauth::where("user_id", $user->id)->first();
            $old = Token::where("user_id", $user->id)->orderBy("created_at", "desc")->first();
            if($old != null)
            {
                if($twitch == null)
                {
                   $twitch = new TwitchOauth();
                }
                $twitch->refresh_token_tw = $old->refresh_token_tw;
                $twitch->access_token_tw = $old->access_token_tw;
                $twitch->expires_in_tw = $old->expires_in_tw;
                $twitch->save();
            }
            
        }

        Schema::table('tokens', function($table)
        {
            $table->dropColumn('access_token_tw');
            $table->dropColumn('refresh_token_tw');
            $table->dropColumn('expires_in_tw');
        });
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('twitchoauth');
    }
}

