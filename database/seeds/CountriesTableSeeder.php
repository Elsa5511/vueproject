<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class CountriesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Country::truncate();
        $path = __DIR__ . '/../scripts/countries.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Countries table seeded!');
    }

}
