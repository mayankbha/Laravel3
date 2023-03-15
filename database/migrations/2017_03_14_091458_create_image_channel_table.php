<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias');
            $table->string('name');
            $table->timestamps();
        });
        Schema::table('images', function (Blueprint $table) {
            $table->integer('channel_id')->nullable()->default(0);
            $table->integer('user_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('image_channel');
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('channel_id');
            $table->dropColumn('user_id');
        });
    }
}
