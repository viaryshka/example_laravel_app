<?php

namespace App\Services;

use App\Events\ChatMessageEvent;
use App\Events\ChatSeenEvent;
use App\Facades\Uploader;
use App\Models\Attachment;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\ChatMessageNotification;
use App\Repositories\AttachmentRepository;
use App\Repositories\ChatRepository;
use Cache;
use  Illuminate\Http\UploadedFile;

class ChatService
{
    private $chatRepo;
    private $attachmentRepo;

    public function __construct(ChatRepository $messageRepository, AttachmentRepository $attachmentRepo)
    {
        $this->chatRepo = $messageRepository;
        $this->attachmentRepo = $attachmentRepo;
    }

    /**
     * @param  User  $fromUser
     * @param  User  $toUser
     * @param  array  $input
     * @return Message
     */
    public function sendMessageToUser(User $fromUser, User $toUser, array $input)
    {
        $chat = $this->chatRepo->getChatBetweenUsers($fromUser, $toUser);
        $message = $this->chatRepo->createChatMessage($chat, $fromUser, $input);
        $message->loadMissing(['attachments']);
        broadcast(new ChatMessageEvent($message, $toUser));
        $toUser->notify(new ChatMessageNotification($message));
        $message->unsetRelation('user');

        return $message;
    }

    /**
     * upload image for new property.
     *
     * @param  mixed  $content
     * @return Attachment
     */
    public function uploadAttachment(UploadedFile $content)
    {
        $extension = $content->getClientOriginalExtension();
        $name = Uploader::generateName($extension);
        $path = Uploader::checkAndPut($name, $content);
        $originalName = $content->getClientOriginalName();

        return $this->attachmentRepo->create($path, $originalName, Attachment::TYPE_UPLOADED_FOR_MESSAGE);
    }

    /**
     * @param  User  $user
     * @return \App\Models\Chat[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getUserChats(User $user)
    {
        return $this->chatRepo->getUserChats($user);
    }

    /**
     * @param  User  $fromUser
     * @param  User  $toUser
     * @param  int  $lastId
     * @param  string  $direction
     * @return \App\Models\Message[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getMessagesBetweenUsers(
        User $fromUser,
        User $toUser,
        int $lastId = 0,
        string $direction = 'prev'
    ) {
        $chat = $this->chatRepo->getChatBetweenUsers($fromUser, $toUser);

        return $this->chatRepo->getChatMessages($chat, $lastId, $direction);
    }

    /**
     * @param  User  $fromUser
     * @param  User  $toUser
     * @return bool
     */
    public function makeMessagesSeenBetweenUsers(User $fromUser, User $toUser)
    {
        $chat = $this->chatRepo->getChatBetweenUsers($fromUser, $toUser);

        $this->chatRepo->makeChatMessagesSeen($chat);
        broadcast(new ChatSeenEvent($toUser, $fromUser, $chat->id));

        return true;
    }

    /**
     * @param  Message  $message
     * @return mixed
     */
    public function deleteMessage(Message $message)
    {
        return $this->chatRepo->deleteMessage($message);
    }

    /**
     * @param  User  $authUser
     * @param  User  $user
     * @param  string  $type
     * @return int
     */
    public function getMinMaxMessageIdBetweenUsers(User $authUser, User $user, string $type)
    {
        $chat = $this->chatRepo->getChatBetweenUsers($authUser, $user);

        return $this->getMinMaxChatMessageId($chat, $type);
    }

    /**
     * @param  Chat  $chat
     * @param  string  $type
     * @return int
     */
    public function getMinMaxChatMessageId(Chat $chat, string $type)
    {
        if ($type == 'min') {
            $key = Chat::cacheFirstMessageKey($chat->id);
        } else {
            $key = Chat::cacheLastMessageKey($chat->id);
        }
        if (Cache::has($key)) {
            return intval(Cache::get($key));
        }
        $messageId = $this->chatRepo->getMinMaxChatMessageId($chat, $type);

        return intval($messageId);
    }

    /**
     * @param  Chat  $chat
     * @param  string  $type
     * @param  null  $messageId
     */
    public function updateMinMaxChatMessageIdCache(Chat $chat, string $type, $messageId = null)
    {
        if ($type == 'min') {
            $key = Chat::cacheFirstMessageKey($chat->id);
        } else {
            $key = Chat::cacheLastMessageKey($chat->id);
        }
        if (! $messageId) {
            $messageId = $this->chatRepo->getMinMaxChatMessageId($chat, $type);
        }
        if ($messageId && $messageId > 0) {
            Cache::forever($key, $messageId);
        } else {
            Cache::forget($key);
        }
    }
}
