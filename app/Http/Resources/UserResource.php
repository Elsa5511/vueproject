<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (auth()->user() && (auth()->user()->role == 'ADMIN' || auth()->user()->id == $this->id)) {
            $this->referer_code;
            return parent::toArray($request);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'dob' => (new \Carbon\Carbon($this->dob))->toDateString(),
            'gender' => $this->gender,
            'role' => $this->role,
            'rate' => $this->rate,
            'avatar_url' => $this->avatar_url,
            'small_avatar_url' => $this->small_avatar_url,
            'medium_avatar_url' => $this->medium_avatar_url,
            'large_avatar_url' => $this->large_avatar_url,
            'original_avatar_url' => $this->original_avatar_url,
            'presence' => $this->presence,
            'country' => $this->country,
            'rate' => $this->rate,
            'identity_verified' => $this->identity_verified,
            'show_notifications' => $this->show_notifications,
            'tags' => $this->tags,
            'referer_code' => $this->referer_code,
            $this->mergeWhen($this->profile, [
                'profile' => new ProfileInUserResource($this->profile),
            ]),
            $this->mergeWhen($this->role == 'PROVIDER', [
                'work_status' => $this->work_status,
                'prices' => $this->prices,
            ]),
            $this->mergeWhen(auth()->user() && (auth()->user()->role == 'ADMIN' || auth()->user()->id == $this->id), [
                'email' => $this->email,
            ]),
            $this->mergeWhen($this->position, [
                'position' => $this->position,
            ]),
            'created_at' => (new \Carbon\Carbon($this->created_at))->toDateTimeString(),
            'updated_at' => (new \Carbon\Carbon($this->updated_at))->toDateTimeString(),
        ];
    }
}
