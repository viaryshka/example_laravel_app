<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'id'   => $this->id,
            'name' => $this->name,
            'properties_count' => $this->properties_count,
            $this->mergeWhen($this->user->id === auth()->id(), [
                'is_default' => $this->is_default,
            ]),
        ];
    }
}
