<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionBoomMeterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_boom_meter', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('boom_meter_type_id')->nullable();
            $table->datetime('starttime');
            $table->datetime('stoptime')->nullable();
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
        Schema::drop('session_boom_meter');
    }
}
