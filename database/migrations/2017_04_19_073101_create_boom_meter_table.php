<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoomMeterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boom_meter', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_code');
            $table->integer('status');
            $table->boolean('custom_img')->default(0);
            $table->boolean('custom_style')->default(0);
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
        Schema::drop('boom_meter');
    }
}
