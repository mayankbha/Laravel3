<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventVodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_vods', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name");
            $table->string("jumbotron_url");
            $table->text("vod_360_url");
            $table->string("game_name");
            $table->string("team_name");
            $table->string("map_name");
            $table->integer("map_id");
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
        Schema::drop('event_vods');
    }
}
