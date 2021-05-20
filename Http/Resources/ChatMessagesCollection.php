<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatMessagesCollection extends ResourceCollection
{
    private $firstId;
    private $lastId;
    private $user;

    public function __construct($resource, $user, $firstId, $lastId)
    {
        parent::__construct($resource);
        $this->lastId = $lastId;
        $this->firstId = $firstId;
        $this->user = $user;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => MessageResource::collection($this->collection),
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [
                'current_first_id' => $this->collection->min->id,
                'current_last_id'  => $this->collection->max->id,
                'chat_first_id'    => $this->firstId,
                'chat_last_id'     => $this->lastId,
                'user'             => new ChatParticipantResource($this->user),
            ],
        ];
    }
}
