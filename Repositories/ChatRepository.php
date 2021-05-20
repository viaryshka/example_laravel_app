<?php

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Traits\Pageable;

class ChatRepository
{
    use Pageable;

    /**
     * @param  User  $user
     * @return Chat[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getUserChats(User $user)
    {
        return $user->chats()
                    ->with(['users', 'lastMessage', 'lastMessage.attachments'])
                    ->withCount('myUnreadMessages')
                    ->joinLastMessage()
                    ->orderBy('lm.created_at', 'DESC')
                    ->get();
    }

    /**
     * @param $user1
     * @param $user2
     * @return Chat
     */
    public function getChatBetweenUsers($user1, $user2)
    {
        $chat = Chat::betweenUsers($user1, $user2)->first();
        if (! $chat) {
            $chat = Chat::create();
            $chat->users()->save($user1);
            $chat->users()->save($user2);
        }

        return $chat;
    }

    /**
     * @param  Chat  $chat
     * @param  User  $user
     * @param  array  $input
     * @return Message
     */
    public function createChatMessage(Chat $chat, User $user, array $input)
    {
        $message = new Message();
        $message->fill($input);
        $message->seen = false;
        $message->user()->associate($user);
        $message->chat()->associate($chat);
        $message->save();
        if (array_key_exists('attachments', $input) && is_array($input['attachments'])) {
            $this->attachFiles($message, $input['attachments']);
        }

        return $message;
    }

    /**
     * attach filer for message in db.
     *
     * @param  Message  $message
     * @param  array  $attachments
     */
    public function attachFiles(Message $message, array $attachments)
    {
        foreach ($attachments as $key => $attachmentId) {
            /* @var Attachment $attachment */
            $attachment = Attachment::where('type', Attachment::TYPE_UPLOADED_FOR_MESSAGE)
                                    ->whereNull('attachable_type')
                                    ->where('id', $attachmentId)
                                    ->first();
            if ($attachment) {
                $attachment->type = null;
                $attachment->attachable()->associate($message);
                $attachment->save();
            }
        }
    }

    /**
     * @param  Chat  $chat
     * @param  int  $lastId
     * @param  string  $direction
     * @return Message[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getChatMessages(Chat $chat, int $lastId = 0, string $direction = 'prev')
    {
        $dateOrder = 'DESC';

        if ($direction == 'prev') {
            $idOperator = '<';
        } else {
            $idOperator = '>';
            $dateOrder = 'ASC';
        }
        $builder = $chat->messages()
                        ->with(['attachments'])
                        ->limit(20)
                        ->orderBy('created_at', $dateOrder);
        if ($lastId > 0) {
            $builder->where('id', $idOperator, $lastId);
        }

        return $builder->get();
    }

    /**
     * @param  Chat  $chat
     */
    public function makeChatMessagesSeen(Chat $chat)
    {
        $chat->messages()
             ->where('seen', 0)
             ->update([
                 'seen' => 1,
             ]);
    }

    /**
     * @param  Message  $message
     * @return bool
     */
    public function deleteMessage(Message $message)
    {
        try {
            $message->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param  Chat  $chat
     * @param  string  $type
     * @return mixed
     */
    public function getMinMaxChatMessageId(Chat $chat, string $type)
    {
        if ($type == 'min') {
            $result = $chat->messages()->min('id');
        } else {
            $result = $chat->messages()->max('id');
        }

        return intval($result);
    }
}
