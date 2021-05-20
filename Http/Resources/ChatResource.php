<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $id = auth()->id();

        return [
            'id'           => $this->id,
            'participant'  => new ChatParticipantResource($this->getParticipant($id)),
            'last_message' => new MessageResource($this->whenLoaded('lastMessage')),
            'unread_count' => $this->my_unread_messages_count,
        ];
    }
}
