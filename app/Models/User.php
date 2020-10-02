<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

//use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class User extends Authenticatable implements JWTSubject, \App\Lib\IFileOwner, \App\Lib\IImageOwner
{

    const ROLES = ['USER', 'AGENT', 'PROVIDER', 'ADMIN', 'CHAT_BOT', 'SYSTEM', 'GUEST'];
    const PRESENCE_STATUS = ['ONLINE', 'OFFLINE', 'UNKNOWN', 'WORKING'];
    const STATUS = ['ACTIVE', 'INACTIVE', 'BLACKLISTED', 'DELETED', 'PENDING', 'CANNOT_LOGIN'];
    const GENDER = ['MALE', 'FEMALE', 'TRANSEXUAL'];
    const WORK_STATUS = ['UNAVAILABLE', 'AVAILABLE', 'UNKNOWN', 'HIDDEN'];

    public $incrementing = false;

    use Notifiable,
        Geographical,
        ImageOwnerModel;

    protected static $kilometers = true;

    const LATITUDE = 'position.latitude';
    const LONGITUDE = 'position.longitude';

    //use SpatialTrait;


    protected $section = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'email_token', 'phone', 'phone_token', 'email_verified', 'phone_verified', 'country_id', 'presence', 'status', 'attributes', 'dob', 'gender', 'role', 'tags', 'referer_id', 'referer_code'
    ];
    protected $_country = null;
    /* protected $spatialFields = [
      'location'
      ];
      protected $appends = [
      'position',
      ]; */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'location'
    ];
    protected $appends = [
        'avatar_url',
        'xsmall_avatar_url',
        'small_avatar_url',
        'medium_avatar_url',
        'large_avatar_url',
        'original_avatar_url',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function country()
    {
        return $this->hasOne('\App\Models\Country', 'id', 'country_id');
    }

    public function referer()
    {
        return $this->belongsTo(User::class, 'referer_id', 'id');
    }

    public function completePhone()
    {
        $completePhone = "+" . $this->country->phonecode . $this->phone;
        return $completePhone;
    }

    public function tokens()
    {
        return $this->hasMany(UserToken::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id')->withDefault();
    }

    public function gallery()
    {
        return $this->hasOne('App\Models\Gallery', 'owner_id', 'id')->where('owner_type', User::class)->withDefault();
    }

    public function position()
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }

    public function refereds()
    {
        return $this->hasMany(User::class, 'referer_id', 'id');
    }

    public function idVerificationRequest()
    {
        return $this->hasOne(IdentityVerificationRequest::class, 'id', 'id_verification_request');
    }

    public function getRefererCodeAttribute()
    {
        if ($this->role != 'GUEST' && !$this->attributes['referer_code']) {
            $this->attributes['referer_code'] = \App\Lib\Utils::generate_unique_code(8);
            $this->save();
        }
        return isset($this->attributes['referer_code']) ? $this->attributes['referer_code'] : '';
    }

    public function services()
    {
        return $this->hasMany(Service::class)
            ->using(ProviderService::class);
    }

    public function prices()
    {
        return $this->hasMany(ProviderPrice::class, 'provider_id', 'id');
    }

    public function blockedZones()
    {
        return $this->hasMany(UserBlockedZone::class, 'user_id', 'id');
    }

    public function appointments()
    {
        return Appointment::where(function ($q) {
            $q->where('customer_id', '=', $this->id)->orWhere('provider_id', '=', $this->id);
        });
    }

    protected function getFileFieldValue()
    {
        if ($this->role === 'GUEST') {
            \Illuminate\Support\Facades\File::copy(public_path('img/defaults/user.jpg'), storage_path('uploads/users/default.jpg'));
            $this->avatar = 'default.jpg';
        }
        if (!$this->avatar) {
            \Illuminate\Support\Facades\File::copy(public_path('img/defaults/user.jpg'), storage_path('uploads/users/' . $this->id . '.jpg'));
            $this->avatar = $this->id . '.jpg';
            $this->save();
        }
        return $this->avatar ? $this->avatar : $this->id . '.jpg';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->getOriginalImageUrl();
    }

    public function getSmallAvatarUrlAttribute()
    {
        return $this->getImageUrl('50x50');
    }

    public function getMediumAvatarUrlAttribute()
    {
        return $this->getImageUrl('200x200');
    }

    public function getLargeAvatarUrlAttribute()
    {
        return $this->getImageUrl('500x500');
    }

    public function getOriginalAvatarUrlAttribute()
    {
        return $this->getImageUrl('0x0');
    }

    public function getXsmallAvatarUrlAttribute()
    {
        return $this->getImageUrl('30x30');
    }

    protected function setFileFieldValue($value)
    {
        $this->avatar = $value;
    }

    public function hasCompleteProfileAttribute()
    {
        return $this->profile->is_complete;
    }

    protected function getOwnerPathName()
    {
        return '0';
    }

    public function getFolder()
    {
        return $this->getSection();
    }

    public function reviews($role = null)
    {
        if (!$role) {
            $role = $this->role;
        }
        return $this->hasMany(UserRate::class, 'user_id', 'id');
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'notifications.' . $this->id;
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        $where = [];
        $ret = [];
        $tokens = $this->tokens()->where('push_token', '!=', null);
        if ($notification->getData()->getDestType() != 'all') {
            if ($notification->getData()->getDestType() == 'mobile') {
                $tokens->where(function ($q) {
                    $q->where('client_type', '=', 'android')->orWhere('client_type', '=', 'ios');
                });
            } else {
                $tokens->where('client_type', '=', 'browser');
            }
        }
        $tokens = $tokens->get();
        foreach ($tokens as $t) {
            $ret[] = $t->push_token;
        }
        return $ret;
    }

    public static function getValidator($data, $instance)
    {
        $rules = [
            ''
        ];
        $validator = validator($data, $rules);
        return $validator;
    }

    public function getPrices($displayCurrency = 'USD')
    {
        $prices = [];

        if (!count($this->prices)) {
            $options = config('custom.price_options');
            $prices = [];
            foreach ($options as $option) {
                $price = new ProviderPrice([
                    'hours' => $option,
                    'currency' => $displayCurrency,
                    'value' => 0
                ]);
                $prices[$option] = $price;
            }
        } else {

            if ($displayCurrency != 'USD') {
                $currency = \App\Models\CurrencyValue::where(['currency' => $displayCurrency])->first();
                if ($currency) {
                    $myPrices = $this->prices()->get();
                    foreach ($myPrices as $price) {
                        $price->currency = $displayCurrency;
                        $price->value = ($price->value * ($currency->value / 100));
                        $prices[$price->hours] = $price;
                    }
                }
            } else {
                foreach ($this->prices as $price) {
                    $prices[$price->hours] = $price;
                }
            }
        }
        return $prices;
    }

    public function getPriceForHours($hours = 1, $displayCurrency = 'USD')
    {
        $prices = $this->getPrices($displayCurrency);
        if (isset($prices[$hours])) {
            return $prices[$hours];
        }
        return 0;
    }

    public function isAvailableForDate($date_from, $date_to, $useSpan = true)
    {

        $dateFrom = \Carbon\Carbon::createFromTimestamp($date_from->getTimestamp());
        $dateTo = \Carbon\Carbon::createFromTimestamp($date_to->getTimestamp());

        if ($useSpan) {
            $minHoursBetweenAppointments = \App\Lib\SettingsManager::getValue('appointments_min_interval', 1, 'float', true);
            $dateFrom->addHours(-$minHoursBetweenAppointments)->addMinutes(1);
            $dateTo->addHours($minHoursBetweenAppointments)->addMinutes(-1);
        }

        $user = $this;
        $appointments = \App\Models\Appointment::where(function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('date_from', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])->orWhereBetween('date_to', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')]);
        })->where('accepted', '=', '1')->where('finished', '=', '0')->where('status_name', '!=', 'finalized')->where('status_name', '!=', 'cancelled')->where('status_name', '!=', 'expired')->where(function ($q) use ($user) {
            $q->where('provider_id', '=', $user->id)->orWhere('customer_id', '=', $user->id);
        })->count();
        return $appointments === 0;
    }

    public function getAvailabilityForDate($date)
    {
        $ret = [];
        $user = $this;
        $dateFrom = \Carbon\Carbon::createFromTimestamp($date->getTimestamp())->setTime(0, 0, 0);
        $dateTo = \Carbon\Carbon::createFromTimestamp($date->getTimestamp())->setTime(23, 59, 59);
        $now = \Carbon\Carbon::now();
        $hours = [];
        $interval = config('custom.reservation_interval');
        $start = \Carbon\Carbon::createFromTimestamp($date->getTimestamp())->setTime(0, 0, 0);
        $end = \Carbon\Carbon::createFromTimestamp($date->getTimestamp())->setTime(23, 59, 59);
        while ($start < $end) {
            $hours[] = $start->format('H:i');
            $start->addMinutes($interval);
        }


        $appointments = \App\Models\Appointment::where(function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('date_from', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')])->orWhereBetween('date_to', [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')]);
        })->where('accepted', '=', '1')->where('finished', '=', '0')->where('status_name', '!=', 'finalized')->where('status_name', '!=', 'cancelled')->where('status_name', '!=', 'expired')->where(function ($q) use ($user) {
            $q->where('provider_id', '=', $user->id)->orWhere('customer_id', '=', $user->id);
        })->get()->all();
        $minHoursBetweenAppointments = \App\Lib\SettingsManager::getValue('appointments_min_interval', 1, 'float', true);
        $minHoursBeforeAppointments = \App\Lib\SettingsManager::getValue('appointments_min_anticipation', 1, 'float', true);
        $minTimeAmount = config('custom.min_reservation_hours');



        foreach ($appointments as $appointment) {
            $appointment->date_from = $appointment->date_from->addHours(-$minHoursBetweenAppointments)->addMinutes(-1);
            $appointment->date_to = $appointment->date_to->addHours($minHoursBetweenAppointments)->addMinutes(1);
        }

        foreach ($hours as $hour) {
            $date->setTime(explode(':', $hour)[0], explode(':', $hour)[1], 0);
            $available = ($now->diffInMinutes($date, false)) >= $minHoursBeforeAppointments * 60 ? 'available' : 'invalid';
            if ($available === 'available') {
                foreach ($appointments as $appointment) {
                    if ($available == 'available') {
                        $available = (!$date->between($appointment->date_from, $appointment->date_to, false)) ? 'available' : 'occupied';
                        if ($available == 'available') {
                            $available = $date->diffInMinutes(\Carbon\Carbon::createFromTimestamp($appointment->date_from->getTimestamp())) >= ($minTimeAmount * 60) ? 'available' : 'invalid_offset';
                        }
                    }
                }
            }
            $ret[$hour] = $available;
        }
        return $ret;
    }

    public function getTagsAttribute()
    {
        if (isset($this->attributes['tags'])) {
            return explode(',', $this->attributes['tags']);
        }
        return [];
    }

    public function transformToPresence()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'presence' => $this->presence,
            'work_status' => $this->work_status,
        ];
    }
}
