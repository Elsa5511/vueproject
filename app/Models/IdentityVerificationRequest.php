<?php

namespace App\Models;

class IdentityVerificationRequest extends UUIDModel implements \App\Lib\IFileOwner, \App\Lib\IImageOwner {

    protected $section = 'id_verifications';
    protected $casts = ['data' => 'array'];
    protected $guarded = [];
    protected $appends = [
        'front_image_url',
        'back_image_url',
        'back_image_href',
        'front_image_href',
    ];

    use \App\Models\ImageOwnerModel;

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function country() {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function getIdImagesFolder() {
        return $this->getFolder() . DIRECTORY_SEPARATOR . 'id_verifications';
    }

    public function getIdImages() {
        $attributes = $this->data;
        $images = \App\Lib\Utils::getByKey('id_images', $attributes, array());
        return $images;
    }

    public function saveIdImages($images) {
        $currImages = [];
        foreach ($images as $key => $image) {
            if ($image && $image->getFilename()) {
                $fileName = $image->store($this->getIdImagesFolder(), 'uploads');
                $currImages[$key] = basename($fileName);
            }
        }

        return $currImages;
    }

    public function deleteIdImage($fileName) {

        Storage::disk('uploads')->delete($this->getIdImagesFolder() . '/' . $fileName);
        File::delete(File::glob(public_path('cache/images/' . $this->getIdImagesFolder() . '/*/' . $fileName)));
    }

    public function syncIdImages() {
        $images = \App\Lib\Utils::getByKey('id_images', $this->attrs);
        $files = Storage::disk('uploads')->files($this->getIdImagesFolder());

        foreach ($files as $file) {
            if (!in_array(basename($file), $images)) {
                $this->deleteIdImage(basename($file));
            }
        }
    }

    public function getFrontImageUrlAttribute() {
        $images = $this->getIdImages();
        if (isset($images['front'])) {
            return $this->getIdImageUrl($images['front'], '250x250');
        }
        return null;
    }

    public function getBackImageUrlAttribute() {
        $images = $this->getIdImages();
        if (isset($images['back'])) {
            return $this->getIdImageUrl($images['back'], '250x250');
        }
        return null;
    }

    public function getFrontImageHrefAttribute() {
        $images = $this->getIdImages();
        if (isset($images['front'])) {
            return $this->getIdImageUrl($images['front'], '0x0');
        }
        return null;
    }

    public function getBackImageHrefAttribute() {
        $images = $this->getIdImages();
        if (isset($images['back'])) {
            return $this->getIdImageUrl($images['back'], '0x0');
        }
        return null;
    }

    public function getIdImageUrl($fileName, $size = '0x0', $ignoreCache = false) {

//$section = str_replace('/', '.', $this->getFolder());
        $url = url('images/' . $this->getFolder() . '.id_verifications/' . $size . '/' . $fileName);
        if ($ignoreCache) {
            $url .= '?no-cache=1';
        }

        $url .= '?no-cache=1';

        /*         * if (!$ignoreCache) {
          if (is_file(public_path('/cache/images/' . $section . '/' . $size . '/' . $this->getFileName()))) {
          return url('/cache/images/' . $section . '/' . $size . '/' . $this->getFileName());
          }
          } */

        return $url;
    }

}
