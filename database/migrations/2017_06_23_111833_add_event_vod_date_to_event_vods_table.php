<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventVodDateToEventVodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('event_vods', function (Blueprint $table) {
            $table->timestamp('vod_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('event_vods', function (Blueprint $table) {
            $table->dropColumn('vod_date');
        });
    }
}
