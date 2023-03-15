<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxOfflineTriedToLiveStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('live_streams', function (Blueprint $table) {
            $table->integer('current_off_request_number')->default(0);
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
        Schema::table('live_streams', function (Blueprint $table) {
            $table->dropColumn('current_off_request_number');
        });
    }
}
