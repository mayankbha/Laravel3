<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class EventVodLiveUrl extends Model
{
    //
    protected $fillable = ['event_vod_id','url'];

    public static function getLiveUrlByVodId($event_vod_id){
        $name_array = ['set_a', 'set_b'];
        $list_live_urls = [];
        foreach ($name_array as $value) {
            $item = EventVodLiveUrl::where('event_vod_id', $event_vod_id)->where('name', $value)->first();
            if ($item) {
                $tmp = explode(",", $item->url);
                $item->jumbotron_url = isset($tmp[0]) ? Helper::event_stream_generate_quality($tmp[0]) : "";
                $item->caster_url = isset($tmp[1]) ? Helper::event_stream_generate_quality($tmp[1]) : "";
                $item->live_1_url = isset($tmp[2]) ? Helper::event_stream_generate_quality($tmp[2]) : "";
                $item->live_2_url = isset($tmp[3]) ? Helper::event_stream_generate_quality($tmp[3]) : "";
                $item->live_3_url = isset($tmp[4]) ? Helper::event_stream_generate_quality($tmp[4]) : "";
                $item->live_4_url = isset($tmp[5]) ? Helper::event_stream_generate_quality($tmp[5]) : "";
                unset($item->name);
                unset($item->id);
                unset($item->created_at);
                unset($item->updated_at);
                unset($item->url);
                unset($item->event_vod_id);
            } else {
                $item = [
                    'jumbotron_url' => "",
                    'caster_url' => "",
                    'live_1_url' => "",
                    'live_2_url' => "",
                    'live_3_url' => "",
                    'live_4_url' => "",
                ];
                $item = json_decode(\GuzzleHttp\json_encode($item));
            }
            $list_live_urls[$value] = $item;
        }
        return $list_live_urls;
    }
}
