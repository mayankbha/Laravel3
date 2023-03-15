<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Video;
use App\Models\VideoGame;

class CreateVideogameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_game', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id');
            $table->integer('video_id');
            $table->timestamps();
        });
        $videos=Video::where("game_id", ">", 0)->get();
        echo "\nTotal: " . $videos->count();
        $i=0;
        foreach ($videos as $key => $v) 
        {
            $i++;
            echo "\n video ".$i." with id: " . $v->id;
            VideoGame::create(["video_id" => $v->id, "game_id" => $v->game_id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('video_game');
    }
}
