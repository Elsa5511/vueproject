<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * Description of ImageOwnerModel
 *
 * @author pramirez
 */
trait ImageOwnerModel {

    protected $image_uuid;

    function getTempId() {
        if ($this->id) {
            return $this->id;
        }
        if (!$this->image_uuid) {
            $this->image_uuid = \Ramsey\Uuid\Uuid::uuid4();
        }
        return $this->image_uuid;
    }

    function setTempId($tempId) {
        $this->image_uuid = $tempId;
    }

    public function getOwnerPathName() {
        return '0';
    }

    public function getOriginalImageUrl($size = '0x0') {
        $section = $this->section;
        $url = url('images/' . $section . DIRECTORY_SEPARATOR . $this->getOwnerPathName() . DIRECTORY_SEPARATOR . $size . '/' . $this->getFileName());
        return $url;
    }

    public function getImageUrl($size = '0x0', $ignoreCache = false) {
        if ($this->getFileFieldValue()) {
            //$section = str_replace('/', '.', $this->getFolder());
            $section = $this->getSection();
            $url = url('images/' . $section . DIRECTORY_SEPARATOR . $this->getOwnerPathName() . DIRECTORY_SEPARATOR . $size . '/' . $this->getFileName());
            if ($ignoreCache) {
                $url .= '?no-cache=1';
            }

           /* if (!$ignoreCache) {
                
                if (is_file(public_path('/cache/images/' . $this->getFolder() . '/' . $size . '/' . $this->getFileName()))) {
                    return url('/cache/images/' . $this->getFolder() . '/' . $size . '/' . $this->getFileName());
                }
            }*/

            return $url;
        }
        return null;
    }

    public function getImageUrlAttribute() {
        return $this->getImageUrl();
    }

    protected function getFileFieldValue() {
        return $this->image;
    }

    protected function setFileFieldValue($value) {
        $this->image = $value;
    }

    public function deleteCache() {
        if ($this->getFileFieldValue() != '') {
            File::delete(File::glob(public_path('cache/images/' . $this->getFolder() . '/*/' . $this->getFileName())));
        }
    }

    public function afterDeleteImage() {
        
    }

    public function deleteImage() {
        if ($this->getFileName() != '') {
            $this->deleteCache();
            Storage::disk('uploads')->delete($this->getFolder() . '/' . $this->getFileName());
        }
        $this->afterDeleteImage();
    }

    public function saveImage($file) {
        if ($file) {
            $this->deleteImage();
            $fileName = $file->store($this->getFolder(), 'uploads');
            $this->setFileFieldValue(basename($fileName));
        }
    }

    public function getFolder() {
        return $this->getSection() . DIRECTORY_SEPARATOR . $this->getOwnerId();
    }

    public function getFileName() {
        return $this->getFileFieldValue();
    }

    public function getOwnerId() {
        return $this->id;
    }

    public function getSection() {
        return $this->section;
    }

    public function afterMoveTempFiles() {
        
    }

}
