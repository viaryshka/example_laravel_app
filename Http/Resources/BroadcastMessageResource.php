<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BroadcastMessageResource extends JsonResource
{
    private $toUserId;

    public function __construct($resource, $toUserId)
    {
        parent::__construct($resource);
        $this->toUserId = $toUserId;
    }

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
            'is_my'       => $this->user_id == $this->toUserId,
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'  => $this->created_at,
        ];
    }
}
