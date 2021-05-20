<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    const CACHE_FIRST_MESSAGE_KEY = 'first-message';
    const CACHE_LAST_MESSAGE_KEY = 'last-message';

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function hasUser(User $user)
    {
        return $this->users()->where('id', $user->id)->exists();
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->orderBy('created_at', 'DESC');
    }

    public function myUnreadMessages()
    {
        return $this->hasMany(Message::class)->userUnread(auth()->id());
    }

    /**
     * Scope a query chat between 2 users.
     *
     * @param  Builder  $query
     * @param  User  $user1
     * @param  User  $user2
     * @return Builder
     */
    public function scopeBetweenUsers(Builder $query, User $user1, User $user2)
    {
        return $query->withCount('users')
                     ->whereHas('users', function (Builder $builder) use ($user1) {
                         $builder->where('id', $user1->id);
                     })
                     ->whereHas('users', function (Builder $builder) use ($user2) {
                         $builder->where('id', $user2->id);
                     })
                     ->having('users_count', 2);
    }

    /**
     * Scope a query chat with user.
     *
     * @param  Builder  $query
     * @param  User  $user
     * @return Builder
     */
    public function scopeContainsUser(Builder $query, User $user)
    {
        return $query->withCount('users')
                     ->whereHas('users', function (Builder $builder) use ($user) {
                         $builder->where('id', $user->id);
                     });
    }

    /**
     * Scope a join last chat message.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeJoinLastMessage(Builder $query)
    {
        return $query->join('messages as lm', function ($join) {
            $join->on('lm.chat_id', 'chats.id')
                 ->where('lm.id',
                     \DB::raw('(SELECT id FROM messages WHERE messages.chat_id=chats.id ORDER BY messages.created_at DESC limit 1)'));
        });
    }

    /**
     * get chat other participant.
     * @param $userId
     * @return User
     */
    public function getParticipant($userId)
    {
        if ($this->relationLoaded('users')) {
            return $this->users
                ->where('id', '!=', $userId)
                ->first();
        }

        return $this->users()
                    ->where('id', '<>', $userId)
                    ->first();
    }

    /**
     * get cache first message key.
     *
     * @param $id
     * @return string
     */
    public static function cacheFirstMessageKey($id)
    {
        return self::CACHE_FIRST_MESSAGE_KEY.$id;
    }

    /**
     * get cache last message key.
     *
     * @param $id
     * @return string
     */
    public static function cacheLastMessageKey($id)
    {
        return self::CACHE_LAST_MESSAGE_KEY.$id;
    }
}
