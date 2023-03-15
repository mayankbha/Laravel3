<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReplayChannelIdToDiscordInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discord_infos', function (Blueprint $table) {
            $table->string("replay_channel_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discord_infos', function (Blueprint $table) {
            $table->dropColumn('replay_channel_id');
        });
    }
}
