<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscordInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discord_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string("access_token");
            $table->string("token_type");
            $table->dateTime("expire_in");
            $table->string("refresh_token");
            $table->string("scope");
            $table->string("guild_id");
            $table->string("guild_name");
            $table->string("username");
            $table->string("discord_id");
            $table->string("email");
            $table->string("avatar");
            $table->string("nickname");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('discord_infos');
    }
}
