<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageChannel extends Model
{
    protected $table = 'image_channel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'alias','created_at', 'updated_at'];

    public function images()
    {
        return $this->hasMany('App\Models\Image', "channel_id");
    }
    public static function getSourcebyAlias($source)
    {
        $alias=ImageChannel::getAlias($source);
        $temp=ImageChannel::where('alias',$alias)->first();
        return $temp;
    }
    public static function getAlias($source){
        $alias=strtolower(trim(str_replace(" ","",$source)));
        return $alias;
    }

    // Return list ids
    public static function createOrUpdate($string)
    {
        $alias = ImageChannel::getAlias($string);
        $source = null;
        if($alias != '')
        {
            $source=ImageChannel::where('alias',$alias)->first(); 
            if($source == null)
            {
                $source = new ImageChannel();
                $source->name = $string;
                $source->alias = $alias;
                $source->save();
            }
        }
        return $source;
    }
}