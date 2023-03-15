<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLikeSuperByToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->integer('like_super')->nullable()->default(0);
            $table->integer('filter_type')->nullable()->default(0);
            $table->integer('view_week')->nullable()->default(0);
        });
        Schema::table('games', function (Blueprint $table) {
            $table->integer('is_category')->nullable()->default(0);
        });
        Schema::create('view_day', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date')->nullable();
            $table->integer('view_numb')->nullable()->default(0);
            $table->integer('video_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('like_super');
            $table->dropColumn('filter_type');
            $table->dropColumn('view_day');
        });
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('is_category');
        });
        Schema::drop('view_week');
    }
}
