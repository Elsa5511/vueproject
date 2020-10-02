<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        Eloquent::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');


        // $this->call(UsersTableSeeder::class);
        //$this->call(PagesSeeder::class);        
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        //$this->call(UsersTableSeeder::class);
        //$this->call(ServicesTableSeeder::class);
        //$this->call(WorkingCitiesTableSeeder::class);
        //$this->call(GalleriesTableSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
