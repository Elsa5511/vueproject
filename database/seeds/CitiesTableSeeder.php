<?php

use Illuminate\Database\Seeder;
use App\Models\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::truncate();
        $path = __DIR__ . '/../scripts/cities.sql';
        DB::unprepared(file_get_contents($path));
        DB::unprepared('update cities set work_enabled=1 WHERE state_id IN(select id from states where country_id=61)');
        $this->command->info('Cities table seeded!');
        
    }
}
