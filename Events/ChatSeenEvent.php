<?php

namespace App\Events;

use App\Http\Resources\BroadcastUserResource;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSeenEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chatId;
    private $toUser;
    private $formUser;

    /**
     * Create a new event instance.
     *
     * @param  User  $toUser
     * @param  User  $fromUser
     * @param  int  $chatId
     */
    public function __construct(User $toUser, User $fromUser, int $chatId)
    {
        $this->toUser = $toUser;
        $this->chatId = $chatId;
        $this->formUser = $fromUser;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.'.$this->toUser->id);
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->chatId,
            'user'    => new BroadcastUserResource($this->formUser),
        ];
    }
}
