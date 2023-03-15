<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('starttime');
            $table->dropColumn('stoptime');
        });
        Schema::create('session', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->timestamp('starttime');
            $table->timestamp('stoptime');
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
        Schema::drop('session');
    }
}
