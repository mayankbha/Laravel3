<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lang;
use Redis;

class Game extends Model
{
    protected $table = 'games';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'alias','created_at', 'updated_at'];

    /*public function videos()
    {
        return $this->hasMany('App\Models\Video');
    }*/
    public function video_game()
    {
        return $this->hasMany('App\Models\VideoGame');
    }
    
    public static function getGamebyAlias($game)
    {
        
        $alias=Game::getAlias($game);
        $temp=Game::where('alias',$alias)->first();
        return $temp;
    }

    public static function get_category_game(){
        return Game::where('is_category',1)->orderBy('video_count','desc')->get();
    }

    public static function getUserGameList($user){
        //$key_cached = Lang::get('cached.userGameList',['user_id'=>$user->id]);
        //$data = Redis::get($key_cached);
        //$data = \GuzzleHttp\json_decode($data,true);
        return array();
    }

    public static function getAlias($game){
        $alias=strtolower(trim(str_replace(" ","",$game)));
        return $alias;
    }

    // Return list ids
    public static function createOrUpdate($gameString)
    {
        $games = explode(",", $gameString);
        $ids = [];
        foreach ($games as $game) 
        {
            $alias = Game::getAlias($game);
            if($alias != '')
            {
                $temp=Game::where('alias',$alias)->first(); 
                if($temp == null)
                {
                    $temp = new Game();
                    $temp->name = $game;
                    $temp->alias = $alias;
                    $temp->save();
                }
                array_push($ids, $temp->id);
            }
        }
        return $ids;
    }
}