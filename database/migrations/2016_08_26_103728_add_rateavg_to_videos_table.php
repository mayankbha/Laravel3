<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Video;

class AddRateavgToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function($table)
        {
            $table->integer('rateavg')->nullable()->default(0);
        });
        $videos = Video::all();
        foreach ($videos as $v) {
            $v->rateavg = $v->getAvgRating();
            $v->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function($table)
        {
            $table->dropColumn('rateavg');
        });
    }
}
