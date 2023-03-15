<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewerStreamerLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('viewer_stream_logs',function (Blueprint $table){
            $table->increments('id');
            $table->longText('viewer');
            $table->integer('user_id');
            $table->integer('live_stream_id');
            $table->integer('viewer_count');
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
        Schema::drop('viewer_stream_logs');
    }
}
