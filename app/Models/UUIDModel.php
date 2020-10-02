<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of BaseModel
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class UUIDModel extends \Illuminate\Database\Eloquent\Model {

    use ISOTimeFormatTrait;

    public $incrementing = false;

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        if ((!isset($this->attributes[$this->primaryKey])) || (!$this->attributes[$this->primaryKey])) {
            $this->attributes[$this->primaryKey] = \Ramsey\Uuid\Uuid::uuid4();
        }
    }

    /**
     * Boot function from laravel.
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = \Ramsey\Uuid\Uuid::uuid4();
            }
        });
    }

}
