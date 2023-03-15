<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Setting::firstCreate([
            'name' => 'boom_app_download_link',
            'title' => 'Boom app download link',
            'value' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'download_link_status',
            'title' => 'Boom website download link status',
            'value' => 'signup',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'time_between_of_live_streams',
            'title' => 'Time between of live streams',
            'value' => '10800',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'game_name',
            'title' => 'VR app game name',
            'value' => 'CSGO',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'team_name',
            'title' => 'VR app team name',
            'value' => 'BoomTvCsgo',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'comingsoon_date',
            'title' => 'Comming soon date',
            'value' => '',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'event_map_change',
            'title' => 'Event map change mode',
            'value' => 'Automatic',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        Setting::firstCreate([
            'name' => 'allow_ip_list',
            'title' => 'List ip address which allow called set game status',
            'value' => '',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'set_url_a',
            'title' => 'Set of vod url A',
            'value' => '',
            'scope' => 'event-vod-url',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'set_url_b',
            'title' => 'Set of vod url B',
            'value' => '',
            'scope' => 'event-vod-url',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'comingsoon_game_name',
            'title' => 'Comming soon game name',
            'value' => '',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Setting::firstCreate([
            'name' => 'comingsoon_team_name',
            'title' => 'Comming soon team name',
            'value' => '',
            'scope' => 'event',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
