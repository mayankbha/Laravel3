<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueKeyOnUnsubscriberUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('unsubscriber_emails', function (Blueprint $table) {
            $table->dropUnique("unsubscriber_emails_email_unique");
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
        Schema::table('unsubscriber_emails', function (Blueprint $table) {
            $table->unique("email");
        });
    }
}
