<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Viewer extends Model
{
    //
    protected $fillable = ['name'];

    public static function createOrUpdate($data){
        $viewer_exist = Viewer::where('name',$data['name'])->first();
        if ($viewer_exist){
            $viewer_exist->updated_at = Carbon::now();
            $viewer_exist->save();
            return $viewer_exist;
        }
        else{
            $viewer_exist = new Viewer();
            $viewer_exist->name = $data['name'];
            $viewer_exist->save();
            return $viewer_exist;
        }
    }
}
