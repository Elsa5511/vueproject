<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $arr = parent::toArray($request);
        if ($this->actions) {
            $arr['actions'] = $this->actions;
        }
        return $arr;
    }

}
