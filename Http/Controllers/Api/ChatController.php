<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\CreateMessageAttachmentRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\GetChatMessagesRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\ChatMessagesCollection;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;

class ChatController extends Controller
{
    private $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getChats()
    {
        $this->authorize('viewAny', Chat::class);
        $user = \Auth::user();
        $chats = $this->chatService->getUserChats($user);

        return ChatResource::collection($chats)->response();
    }

    /**
     * @param  GetChatMessagesRequest  $request
     * @param  User  $user
     * @return ChatMessagesCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getChatMessagesWithUser(GetChatMessagesRequest $request, User $user)
    {
        $this->authorize('chat', $user);
        $authUser = \Auth::user();

        $lastId = $request->last_message_id ?? 0;
        $direction = $request->direction ?? 'prev';
        $messages = $this->chatService->getMessagesBetweenUsers($authUser, $user, $lastId, $direction);
        $firstChatId = $this->chatService->getMinMaxMessageIdBetweenUsers($authUser, $user, 'min');
        $lastChatId = $this->chatService->getMinMaxMessageIdBetweenUsers($authUser, $user, 'max');

        return new ChatMessagesCollection($messages, $user, $firstChatId, $lastChatId);
    }

    /**
     * Send a message to chat.
     *
     * @param  User  $user
     * @param  CreateMessageRequest  $request
     * @return MessageResource response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendToUser(User $user, CreateMessageRequest $request)
    {
        $this->authorize('chat', $user);
        $message = $this->chatService->sendMessageToUser(\Auth::user(), $user, $request->only([
            'body',
            'attachments',
        ]));

        return new MessageResource($message);
    }

    /**
     * @param  Message  $message
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteMessage(Message $message)
    {
        $this->authorize('delete', $message);

        $deleted = $this->chatService->deleteMessage($message);
        if (! $deleted) {
            abort(400);
        }

        return response()->noContent();
    }

    /**
     * @param  User  $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function makeSeenUserMessages(User $user)
    {
        $this->authorize('chat', $user);
        $authUser = \Auth::user();
        $this->chatService->makeMessagesSeenBetweenUsers($authUser, $user);

        return response()->noContent();
    }

    /**
     * @param  CreateMessageAttachmentRequest  $request
     * @return AttachmentResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function uploadAttachment(CreateMessageAttachmentRequest $request)
    {
        $this->authorize('create', Chat::class);
        $attachment = $this->chatService->uploadAttachment($request->file('attachment'));

        return new AttachmentResource($attachment);
    }
}
