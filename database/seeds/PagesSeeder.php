<?php

use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = \Faker\Factory::create();
        App\Models\Page::create([
            'title'=>'Terminos y condiciones',
            'slug'=>'terms-and-conditions',
            'body'=> Magyarjeti\LaravelLipsum\LipsumFacade::decorate()->html(5),
        ]);
    }

}
