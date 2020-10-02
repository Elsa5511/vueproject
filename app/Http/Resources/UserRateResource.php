<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRateResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return[
            'id' => $this->id,
            'rate' => $this->rate,
            'review' => $this->review,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->created_at->toDateTimeString(),
            $this->mergeWhen($this->author, [
                'author' => new UserResource($this->author),
            ]),
        ];
    }

}
