<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Video;
use App\Models\Game;
class AddGameIdToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->integer('game_id');

        });
        $game=Game::all();
        foreach ($game as $key => $value) {
            Video::where('game',$value->name)->update([
                    'game_id'=>$value->id,
                ]);  
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('game_id');
        });
    }
}
