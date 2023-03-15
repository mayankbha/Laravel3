<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventVod extends Model
{
    //
    protected $fillable = ['name','jumbotron_url','360_vod_url','team_name','game_name','map_name','map_id'];

    public static function firstCreate($data){
        $first_data = static::where('name',$data['name'])->first();
        if (!$first_data){
            $event_vod = new EventVod($data);
            $event_vod->save();
            return $event_vod->id;
        }
        else{
            return $first_data->id;
        }
    }

}
