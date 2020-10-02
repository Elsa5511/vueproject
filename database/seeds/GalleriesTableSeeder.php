<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gallery;
use App\Models\Resource;
use App\Models\ResourceImage;

class GalleriesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Gallery::truncate();
        Resource::truncate();
        $faker = \Faker\Factory::create();
        Storage::disk('cache')->deleteDirectory('images/galleries');
        Storage::disk('uploads')->deleteDirectory('galleries');
        Storage::disk('uploads')->makeDirectory('galleries');

        $providers = User::where(['role' => 'PROVIDER'])->get();

        foreach ($providers as $user) {

            $gallery = $user->gallery;
            if ($user->gallery->isDirty()) {
                $gallery->id = \Ramsey\Uuid\Uuid::uuid4();
                $gallery->owner_id = $user->id;
                $gallery->owner_type = User::class;
                $gallery->save();
                File::makeDirectory(storage_path('uploads/galleries/' . $gallery->id . '/'));
            }

            $files = rand(3, 20);
            for ($i = 0; $i < $files; $i++) {

                $resource = new App\Models\GalleryImage();
                $id = \Ramsey\Uuid\Uuid::uuid4();
                $resource->id = $id;
                
                $file = $faker->image($dir = storage_path('uploads/galleries/' .  $gallery->id . '/'), $width = 1024, $height = 768);
                $resource->uri = basename($file);
                $resource->owner_type = Gallery::class;
                $resource->mime_type = File::mimeType($file);
                $resource->size = File::size($file) / 1024;
                $gallery->resources()->save($resource);
            }

            $gallery->save();
        }
    }

}
