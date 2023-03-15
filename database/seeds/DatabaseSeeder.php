<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(EventTableSeeder::class);
        //$this->call(AdminTableSeeder::class);
        //$this->call(SettingsTableSeeder::class);
        $this->call(BoomMeterTypeSeeder::class);
    }
}
