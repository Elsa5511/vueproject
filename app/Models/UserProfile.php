<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends UUIDModel {

    protected $table = 'user_profiles';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id', 'first_name', 'last_name', 'contact_email', 'identity_id', 'attributes', 'sexual_orientation', 'eyes_color', 'corporal_complexion', 'corporal_dimensions',
        'height', 'weight', 'hourly_rate', 'skin_color', 'hair_color', 'country_id', 'languages'];
    /* protected $appends = [
      'avatar_url',
      'small_avatar_url',
      'medium_avatar_url',
      ]; */
    protected $appends = [
        'is_complete'
    ];
    protected $cats = [
        'height' => 'float',
        'weight' => 'float',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function country() {
        return $this->hasOne('\App\Models\Country', 'id', 'country_id');
    }

    public function getIsCompleteAttribute() {
        if ($this->user && $this->user->role === 'PROVIDER') {
            return $this->country_id && $this->sexual_orientation && $this->eyes_color && $this->corporal_complexion && $this->weight;
        } else {
            return true;
        }
    }

    public function getCompleteName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /* public function getAvatarUrlAttribute() {
      return $this->user->getAvatarUrlAttribute();
      }

      public function getSmallAvatarUrlAttribute() {
      return $this->user->getImageUrl('50x50');
      }

      public function getMediumAvatarUrlAttribute() {
      return $this->user->getImageUrl('200x200');
      } */
}
