<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('email');
            $table->string('type');
            $table->string("timezone");
            $table->time("sent_at");
            $table->integer('current_status');
            $table->integer('current_template');
            $table->integer('interval_status');
            $table->dateTime('first_sent_at');
            $table->dateTime('user_created_at');
            $table->dateTime('last_video_created_at');
            $table->integer('number_video');
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
        Schema::drop('user_reminders');
    }
}
