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
 * Description of ResourceImage
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class ResourceImage extends Resource implements \App\Lib\IFileOwner, \App\Lib\IImageOwner {

    protected $table = 'resources';
    public $appends = ['url'];

    use ImageOwnerModel;

    public $incrementing = false;
    protected $section = 'resources';

    public static function boot() {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('type', 'IMAGE');
        });
    }

    public function getOwnerPathName() {
        return $this->id;
    }

    protected function getFileFieldValue() {
        return $this->uri;
    }

    protected function setFileFieldValue($value) {
        $this->uri = $value;
    }

    public function getUrlAttribute() {
        return $this->getImageUrl();
    }

    public function deleteCache() {
        if ($this->getFileFieldValue() != '') {
            File::deleteDirectory(public_path('cache/images/' . $this->getFolder()));
        }
    }

}
