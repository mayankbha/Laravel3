<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $table = 'settings';

    protected $fillable = ['name','value','title'];

    public static function firstCreate($data){
        $first_data = Setting::where('name',$data['name'])->first();
        if (!$first_data){
            $setting = new Setting($data);
            $setting->save();
            return $setting->id;
        }
        else{
            return $first_data->id;
        }
    }

    public static function all_setting(){
        $settings = Setting::all();
        $return_data = new Collection();
        foreach ($settings as $item){
            $return_data->add($item);
        }
        $return_data = $return_data->keyBy('name');
        return $return_data;
    }
}
