<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsorshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsorship', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->datetime('starttime');
            $table->datetime('expiredtime');
            $table->string('video_link');
            $table->string('timezone')->default("PST");
            $table->integer('duration');
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
         Schema::drop('sponsorship');
    }
}
