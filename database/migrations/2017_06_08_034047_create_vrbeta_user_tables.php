<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVrbetaUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vrbeta_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string("twitch_id");
            $table->string("code");
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
        Schema::drop('vrbeta_users');
    }
}
