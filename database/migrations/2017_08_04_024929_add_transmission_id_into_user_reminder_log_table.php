<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransmissionIdIntoUserReminderLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_reminder_mail_logs', function (Blueprint $table) {
            $table->string('transmission_id');
            $table->string('sparkpost_status');
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
        Schema::table('user_reminder_mail_logs', function (Blueprint $table) {
            $table->dropColumn('transmission_id');
            $table->dropColumn('sparkpost_status');
        });
    }
}
