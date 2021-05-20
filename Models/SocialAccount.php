<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    const PROVIDER_GOOGLE = 'google';
    const PROVIDER_FACEBOOK = 'facebook';

    protected $fillable = [
        'user_id',
        'provider',
        'name',
        'provider_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
