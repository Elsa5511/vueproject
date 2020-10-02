<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderPrice extends Model {

    use ISOTimeFormatTrait;

    protected $guarded = [];

    /**
     * primaryKey 
     * 
     * @var integer
     * @access protected
     */
    protected $primaryKey = 'provider_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

}
