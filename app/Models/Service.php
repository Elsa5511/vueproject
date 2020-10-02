<?php

namespace App\Models;

class Service extends UUIDModel {

    protected $fillable = ['id', 'name', 'description', 'attributes'];

}
