<?php

namespace App\Models;

class UserBlockedZone extends UUIDModel {

    use Geographical;

    protected static $kilometers = true;
    protected $fillable = ['display_name', 'polygon', 'attributes', 'enabled'];

}
