<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends UUIDModel {

   

    protected $fillable = ['user_id', 'provider_id', 'status', 'payment_status', 'payment_method', 'hours', 'date', 'attributes', 'message'];
    protected $cats = [
        //'attributes' => 'array',
        'date_from' => 'datetime:c',
        'date_to' => 'datetime:c',
    ];
    protected $dates = [
        'date_from', 'date_to'
    ];

    const STATUS = [
        'AWAITING_ACCEPTANCE',
        'REJECTED',
        'PENDING',
        'ON_THE_WAY',
        'DELAYED',
        'ARRIVED',
        'IN_PROGRESS',
        'FINALIZED',
        'CANCELLED',
        'UNDONE',
        'DISPUTED',
        'EXPIRED',
    ];
    const PAYMENT_STATUS = [
        'PENDING',
        'PAYED',
        'CANCELLED',
        'FOR_AUTHORIZATION',
        'REJECTED',
    ];
    const PAYMENT_METHOD = [
        'UNDEFINED',
        'CASH',
        'CREDIT_CARD',
        'OTHER',
    ];

    public function status() {
        return $this->hasOne('\App\Models\AppointmentHistory', 'id', 'current_status_id');
    }

    public function history() {
        return $this->hasMany('\App\Models\AppointmentHistory', 'appointment_id', 'id')->orderBy('created_at', 'desc');
    }

    public function customer() {
        return $this->belongsTo('\App\Models\User', 'customer_id', 'id');
    }

    public function provider() {
        return $this->belongsTo('\App\Models\User', 'provider_id', 'id');
    }

    public function providerRate() {
        return $this->hasOne(UserRate::class, 'appointment_id', 'id')->where(['user_role' => 'PROVIDER']);
    }

    public function customerRate() {
        return $this->hasOne(UserRate::class, 'appointment_id', 'id')->where(['user_role' => 'CUSTOMER']);
    }

}
