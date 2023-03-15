<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscriberMonthToViewerUserSub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('viewer_user_sub', function (Blueprint $table) {
            $table->integer('subscriber_month')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('viewer_user_sub', function (Blueprint $table) {
            $table->dropColumn('subscriber_month');
        });
    }
}
