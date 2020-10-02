<?php

namespace Cypretex\Chat;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    protected $prefix = 'mc_';
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
