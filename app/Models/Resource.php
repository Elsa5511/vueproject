<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends UUIDModel {

    const TYPES = ['IMAGE', 'VIDEO', 'YOUTUBE', 'FILE', 'LINK'];

    public $appends = ['url'];

    public function getUrlAttribute() {
        return $this->url;
    }

}
