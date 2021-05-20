<?php

namespace App\Events;

use App\Http\Resources\BroadcastMessageResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    private $toUser;

    /**
     * Create a new event instance.
     *
     * @param  Message  $message
     * @param $toUser
     */
    public function __construct(Message $message, User $toUser)
    {
        $message->loadMissing('user');
        $this->message = $message;
        $this->toUser = $toUser;
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
            'message'      => new BroadcastMessageResource($this->message, $this->toUser->id),
            'unread_count' => $this->toUser->getUnreadMessagesCount(),
        ];
    }
}
