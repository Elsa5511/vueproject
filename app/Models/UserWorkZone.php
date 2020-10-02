<?php

namespace App\Models;

class UserWorkZone extends UUIDModel {

    protected $fillable = [
        'user_id', 'city_id', 'display_name', 'polygon', 'attributes', 'enabled'
    ];

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
