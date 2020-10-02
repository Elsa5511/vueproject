<?php

use Illuminate\Database\Seeder;
use App\Models\UserWorkZone;
use App\Models\User;
use App\Models\City;

class WorkingCitiesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = \Faker\Factory::create();
        UserWorkZone::truncate();
        $providers = User::where(['role' => 'PROVIDER'])->get();
        $cities = City::where(['work_enabled' => 1])->get();

        foreach ($providers as $provider) {
            $city = $faker->randomElement($cities);
            UserWorkZone::create([
                'user_id' => $provider->id,
                'city_id' => $city->id,
                'display_name' => $city->name,
            ]);
        }

        $this->command->info('Work zones table seeded!');
    }

}
