<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('token');
            $table->string('access_token_tw');
            $table->string('refresh_token_tw');
            $table->string('expires_in')->nullable();
            $table->string('expires_in_tw')->nullable();
            $table->timestamps();
        });
        Schema::table('users', function($table)
        {
            $table->dropColumn('token');
            $table->dropColumn('refresh_token');
            $table->dropColumn('access_token');
            $table->dropColumn('expires_in');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tokens');
    }
}
