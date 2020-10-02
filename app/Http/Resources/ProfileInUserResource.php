<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileInUserResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        if (auth()->user() && (auth()->user()->role == 'ADMIN' || auth()->user()->id == $this->id)) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'website' => $this->website,
            'website' => $this->website,
            'linkedin_profile' => $this->linkedin_profile,
            'facebook_profile' => $this->facebook_profile,
            'twitter_profile' => $this->twitter_profile,
            'youtube_profile' => $this->youtube_profile,
            'instagram_profile' => $this->instagram_profile,
            'hair_color' => $this->hair_color,
            'eyes_color' => $this->eyes_color,
            'skin_color' => $this->skin_color,
            'corporal_dimensions' => $this->corporal_dimensions,
            'weight' => $this->weight,
            'corporal_complexion' => $this->corporal_complexion,
            'height' => $this->height,
            'sexual_orientation' => $this->sexual_orientation,
            'hourly_rate' => $this->hourly_rate,
            'country' => $this->country,
            'is_complete' => $this->is_complete,
            'languages' => $this->languages,
        ];
    }

}
