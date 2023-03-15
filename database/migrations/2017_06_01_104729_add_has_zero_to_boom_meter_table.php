<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasZeroToBoomMeterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boom_meter', function (Blueprint $table) {
            $table->boolean('has_zero')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boom_meter', function (Blueprint $table) {
            $table->dropColumn('has_zero');
        });
    }
}
