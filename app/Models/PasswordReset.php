<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends UUIDModel {

    public $primaryKey = 'field';
    public $fillable = ['field', 'token'];
    
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

}
