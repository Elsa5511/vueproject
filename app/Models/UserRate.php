<?php

namespace App\Models;

class UserRate extends UUIDModel {

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function author() {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

}
