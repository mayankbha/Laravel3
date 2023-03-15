<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\BoomMeterType;

class BoomMeterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        BoomMeterType::create([
            'name' => 'Classic Boom',
            'type' => BoomMeterType::DEFAULT_BASIC_TYPE,
            'folders3' => 'defaults/ClassicBoom',
            'image' => 'ClassicBoom.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        BoomMeterType::create([
            'name' => 'Clear Sky',
            'type' => BoomMeterType::DEFAULT_TYPE,
            'folders3' => 'defaults/ClearSky',
            'image' => 'ClearSky.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        BoomMeterType::create([
            'name' => 'Greyscale',
            'type' => BoomMeterType::DEFAULT_TYPE,
            'folders3' => 'defaults/GrayScale',
            'image' => 'GrayScale.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        BoomMeterType::create([
            'name' => 'Acid Green',
            'type' => BoomMeterType::DEFAULT_TYPE,
            'folders3' => 'defaults/AcidGreen',
            'image' => 'AcidGreen.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        BoomMeterType::create([
            'name' => 'SciFi Glow',
            'type' => BoomMeterType::DEFAULT_TYPE,
            'folders3' => 'defaults/SciFiGlowPurple',
            'image' => 'SciFiGlowPurple.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        BoomMeterType::create([
            'name' => 'RedMeter',
            'folders3' => 'defaults/RedMeter',
            'image' => 'RedMeter.png',
            'type' => BoomMeterType::DEFAULT_TYPE,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        BoomMeterType::create([
            'name' => 'Custom',
            'folders3' => '',
            'image' => 'CDStatic.png',
            'type' => BoomMeterType::CUSTOM_TYPE,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
