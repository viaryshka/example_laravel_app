<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'id'          => $this->id,
            'chat_id'     => $this->chat_id,
            'user'        => new ChatParticipantResource($this->whenLoaded('user')),
            'body'        => $this->body,
            'seen'        => $this->seen,
            'is_my'       => $this->user_id == auth()->id(),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'  => $this->created_at,
        ];
    }
}
