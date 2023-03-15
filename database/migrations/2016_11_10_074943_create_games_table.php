<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Video;
use App\Models\Game;
class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('alias');
            $table->timestamps();
        });
         $video=Video::where('game',"<>",null)->get();
         foreach ($video as $key => $value) {
            $game=Game::where('name',$value->game)->first();
            if($game==null)
                Game::create([
                    "name"=>$value->game,
                    "alias"=>strtolower(trim(str_replace(" ","",$value->game))),
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
         Schema::drop('games');
    }
}
