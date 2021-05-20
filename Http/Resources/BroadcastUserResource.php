<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BroadcastUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'email'            => $this->email,
            'name'             => $this->name,
            'avatar'           => optional($this->avatar)->getUrl(),
            'is_online'        => $this->isOnline(),
        ];
    }
}
