<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::truncate();
        $faker = \Faker\Factory::create();
        Storage::disk('cache')->deleteDirectory('images/users');
        Storage::disk('uploads')->deleteDirectory('users');
        Storage::disk('uploads')->makeDirectory('users');

        // Let's make sure everyone has the same password and 
        // let's hash it before the loop, or else our seeder 
        // will be too slow.
        $password = Hash::make('secret');

        $user = User::create([
                    'name' => 'Vaceate chat bot',
                    'email' => 'chatbot@vaceate.com',
                    'password' => $password,
                    'role' => 'CHAT_BOOT',
                    'id' => $faker->uuid,
                    'status' => 'CANNOT_LOGIN',
        ]);

        $user = User::create([
                    'name' => 'Vaceate system user',
                    'email' => 'systemuser@vaceate.com',
                    'password' => $password,
                    'role' => 'SYSTEM',
                    'id' => $faker->uuid,
                    'status' => 'CANNOT_LOGIN',
        ]);

        $user = User::create([
                    'name' => 'Super Administrator',
                    'email' => 'superadmin@test.com',
                    'password' => $password,
                    'role' => 'ADMIN',
                    'id' => $faker->uuid,
                    'status' => 'ACTIVE',
        ]);
        $user->avatar = basename($faker->image($dir = storage_path('uploads/users/'), $width = 640, $height = 480));
        $user->save();

        // And now let's generate a few dozen users for our app:
        /* for ($i = 0; $i < 50; $i++) {
          $user = User::create([
          'name' => $faker->name,
          'dob' => $faker->dateTimeBetween('-50 years', '-18 years'),
          'email' => 'user' . $i . '@test.com',
          'password' => $password,
          'email_verified' => true,
          'role' => 'USER',
          'country_id' => 231,
          'id' => $faker->uuid,
          'status' => 'ACTIVE',
          ]);
          $user->avatar = basename($faker->image($dir = storage_path('uploads/users/'), $width = 640, $height = 480));
          $user->save();
          } */

        // And now let's generate a few dozen users for our app:
        for ($i = 0; $i < 50; $i++) {
            $user = User::create([
                        'name' => $faker->name,
                        'dob' => $faker->dateTimeBetween('-50 years', '-18 years'),
                        'phone' => str_pad($i, 9, "0"),
                        'password' => $password,
                        'phone_verified' => true,
                        'role' => 'PROVIDER',
                        'id' => $faker->uuid,
                        'country_id' => 231,
                        'status' => 'ACTIVE',
            ]);
            $user->avatar = basename($faker->image($dir = storage_path('uploads/users/'), $width = 640, $height = 480));
            $user->save();
        }

        // And now let's generate a few dozen users for our app:
        /* for ($i = 100; $i < 150; $i++) {
          $user = User::create([
          'name' => $faker->name,
          'dob' => $faker->dateTimeBetween('-50 years', '-18 years'),
          'email' => 'provider' . $i . '@test.com',
          'password' => $password,
          'email_verified' => true,
          'role' => 'PROVIDER',
          'country_id' => 231,
          'id' => $faker->uuid,
          'status' => 'ACTIVE',
          ]);
          $user->avatar = basename($faker->image($dir = storage_path('uploads/users/'), $width = 640, $height = 480));
          $user->save();
          } */

        // And now let's generate a few dozen users for our app:
        for ($i = 50; $i < 100; $i++) {
            $user = User::create([
                        'name' => $faker->name,
                        'dob' => $faker->dateTimeBetween('-50 years', '-18 years'),
                        'phone' => str_pad($i, 9, "0"),
                        'password' => $password,
                        'phone_verified' => true,
                        'role' => 'USER',
                        'id' => $faker->uuid,
                        'country_id' => 231,
                        'status' => 'ACTIVE',
            ]);
            $user->avatar = basename($faker->image($dir = storage_path('uploads/users/'), $width = 640, $height = 480));
            $user->save();
        }
        /* $user = User::create([
          'name' => 'Pablo',
          'phone' => '2243186138',
          'dob' => Carbon\Carbon::create(1986, 04, 21),
          'password' => $password,
          'phone_verified' => true,
          'role' => 'USER',
          'id' => $faker->uuid,
          'country_id' => 231,
          'status' => 'ACTIVE',
          ]);
          $user->avatar = basename($faker->image($dir = storage_path('uploads/users/'), $width = 640, $height = 480));
          $user->save(); */
    }

}
