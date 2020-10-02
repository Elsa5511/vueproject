<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = \Faker\Factory::create();
        for ($i = 1; $i <= 25; $i++) {
            \App\Models\Service::create([
                'id' => \Ramsey\Uuid\Uuid::uuid4(),
                'name' => 'Servicio ' . $i,
                'description' => $faker->text($faker->numberBetween(20, 120)),
            ]);
        }
    }

}
