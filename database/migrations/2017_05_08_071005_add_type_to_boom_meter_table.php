<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToBoomMeterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boom_meter', function (Blueprint $table) {
            $table->integer('boom_meter_type_id')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('allow_custom_meter')->default(0);
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
            $table->dropColumn('boom_meter_type_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('allow_custom_meter');
        });
    }
}
