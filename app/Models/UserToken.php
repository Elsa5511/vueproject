<?php

namespace App\Models;

class UserToken extends \Illuminate\Database\Eloquent\Model {

    protected $guarded = [];
    protected $casts = [
        'attributes' => 'array'
    ];

    public function user() {
        if (!$this->is_user) {
            return new User();
        }
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
