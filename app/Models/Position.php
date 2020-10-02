<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends UUIDModel {

    use Geographical;
    protected static $kilometers = true;
    

    protected $casts = [
        'attributes' => 'array'
    ];
    protected $fillable = ['protocol', 'user_id', 'latitude', 'longitude', 'altitude', 'accuracy', 'valid', 'attributes'];

}
