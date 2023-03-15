<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLiveStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('live_streams',function (Blueprint $table){
            $table->increments('id');
            $table->integer('user_id');
            $table->dateTime('started_time');
            $table->dateTime('stopped_time');
            $table->integer('is_live')->default(0);
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
        //
        Schema::drop('live_streams');
    }
}
