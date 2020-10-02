<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\State;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::truncate();
        $path = __DIR__ . '/../scripts/states.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('States table seeded!');
    }
}
