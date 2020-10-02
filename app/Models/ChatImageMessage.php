<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of ChatImageMessage
 *
 * @author pablo
 */
class ChatImageMessage extends ResourceImage {

    public $appends = [
        'small_image_url',
        'medium_image_url',
        'large_image_url',
        'original_image_url',
        'url',
    ];

    public function getSection() {
        return 'chats';
    }

    public function getOwnerPathName() {
        return $this->owner_id;
    }

    public function getOwnerId() {
        return $this->owner_id;
    }

    public function getFolder() {
        return $this->getSection() . DIRECTORY_SEPARATOR . $this->getOwnerId();
    }

    public function deleteImage() {
        if ($this->getFileName() != '') {
            $this->deleteCache();
            Storage::disk('uploads')->delete($this->getFolder() . '/' . $this->getFileName());
        }
        $this->afterDeleteImage();
    }

    public function getSmallImageUrlAttribute() {
        return $this->getImageUrl('50x50');
    }

    public function getMediumImageUrlAttribute() {
        return $this->getImageUrl('300x300');
    }

    public function getLargeImageUrlAttribute() {
        return $this->getImageUrl('500x500');
    }

    public function getOriginalImageUrlAttribute() {
        return $this->getImageUrl('0x0');
    }

    public function deleteCache() {
        if ($this->getFileFieldValue() != '') {
            File::delete(File::glob(public_path('cache/images/' . $this->getFolder() . '/*/' . $this->getFileName())));
        }
    }

    //put your code here
}
