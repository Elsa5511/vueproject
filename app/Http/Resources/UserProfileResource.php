<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        if (auth()->user() && (auth()->user()->role == 'ADMIN' || auth()->user()->id == $this->user_id)) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->user_id,
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
            'avatar_url' => $this->user->avatar_url,
            'small_avatar_url' => $this->user->small_avatar_url,
            'medium_avatar_url' => $this->user->medium_avatar_url,
            'large_avatar_url' => $this->user->large_avatar_url,
            'original_avatar_url' => $this->user->original_avatar_url,
            'hourly_rate' => $this->hourly_rate,
            'country' => $this->country,
            'is_complete' => $this->is_complete,
            'languages' => $this->languages,
            $this->mergeWhen($this->user, [
                'user' => new UserResource($this->user),
                'gender' => $this->user->gender,
                'dob' => $this->user->dob,
                'display_name' => $this->user->name,
            ]),
            $this->mergeWhen($this->actions, [
                'actions' => $this->actions,
            ]),
        ];
    }

}
