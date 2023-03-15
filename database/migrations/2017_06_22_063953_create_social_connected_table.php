<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialConnectedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_connected', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('auto_tweet')->default(0);
            $table->string('type');
            $table->string("token");
            $table->string('social_id')->nullable();
            $table->string('email')->nullable();
            $table->string("token_secret")->nullable();
            $table->string("name")->nullable();
            $table->string("nick_name")->nullable();
            $table->string("avatar")->nullable();
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
        Schema::drop('social_connected');
    }
}
