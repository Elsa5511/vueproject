<?php

namespace App\Models;


class RefererComission extends UUIDModel
{
    protected $guarded = [];

    public function refered()
    {
        return $this->belongsTo('\App\Models\User', 'refered_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'user_id', 'id');
    }

    public function appointment()
    {
        return $this->belongsTo('\App\Models\Appointment', 'appointment_id', 'id');
    }
}
