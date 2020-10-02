<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model {

    use ISOTimeFormatTrait;

    public $incrementing = false;
    protected $table = 'provider_services';
    protected $primaryKey = ['provider_id', 'service_id'];
    protected $fillable = ['provider_id', 'service_id', 'price', 'description', 'attributes'];

}
