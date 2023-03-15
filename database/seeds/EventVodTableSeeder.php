<?php

use Illuminate\Database\Seeder;
use App\Models\EventVod;

class EventVodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        EventVod::firstCreate([
            'name' => "Event vod 1",
            'jumbotron_url' => 'http://main-hls.farm.boom.tv/hls/jumbotron1[quality].m3u8',
            'vod_360_url' => 'http://main-hls.farm.boom.tv/hls360/live1[quality].m3u8,
                               http://main-hls.farm.boom.tv/hls360/live2[quality].m3u8,
                               http://main-hls.farm.boom.tv/hls360/live3[quality].m3u8,
                               http://main-hls.farm.boom.tv/hls360/live4[quality].m3u8',
            'team_name' => 'boomtvCs',
            'game_name' => 'csgo',
            'map_name'  =>  'inferno',
            'map_id'    =>  3,
        ]);

        EventVod::firstCreate([
            'name' => "Event vod 2",
            'jumbotron_url' => 'http://main-hls.farm.boom.tv/hls/jumbotron1[quality].m3u8',
            'vod_360_url' => 'http://main-hls.farm.boom.tv/hls360/live1[quality].m3u8,
                               http://main-hls.farm.boom.tv/hls360/live2[quality].m3u8,
                               http://main-hls.farm.boom.tv/hls360/live3[quality].m3u8,
                               http://main-hls.farm.boom.tv/hls360/live4[quality].m3u8',
            'team_name' => 'boomtvCs',
            'game_name' => 'csgo',
            'map_name'  =>  'inferno',
            'map_id'    =>  3,
        ]);

    }
}
