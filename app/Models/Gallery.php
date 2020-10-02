<?php

namespace App\Models;


class Gallery extends UUIDModel
{
    
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        if((!isset($this->attributes['id']))||!$this->attributes['id']){
            $this->id= \Ramsey\Uuid\Uuid::uuid4();
        }
    }
    
    public function resources(){
        return $this->hasMany(GalleryImage::class, 'owner_id', 'id')->where('owner_type', Gallery::class)->orderBy('created_at', 'desc');
    }
}
