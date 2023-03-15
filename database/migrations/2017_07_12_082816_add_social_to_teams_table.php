<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('website')->nullable()->default("");
            $table->string('twitter_link')->nullable()->default("");
            $table->string('facebook_link')->nullable()->default("");
            $table->string('twitch_link')->nullable()->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('website');
            $table->dropColumn('twitter_link');
            $table->dropColumn('facebook_link');
            $table->dropColumn('twitch_link');
        });
    }
}
