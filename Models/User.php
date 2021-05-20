<?php

namespace App\Models;

use App\Scopes\UserScopes;
use App\Services\CompanyService;
use App\Services\StripeService;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use Notifiable, HasApiTokens, HasRoles, Sortable, Searchable, UserScopes, Billable;

    const CACHE_ONLINE_KEY = 'user-online';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'company_id',
        'lang',
        'referral_id',
        'activated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activated'         => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'email_verified_at',
    ];

    protected $sortable = [
        'name',
        'phone',
        'email',
        'created_at',
        'company',
    ];

    protected $searchFields = [
        'name',
        'email',
        'phone',
        'company_name',
    ];

    public function preferredLocale()
    {
        return $this->lang;
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'user.'.$this->id;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function avatar()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', Attachment::TYPE_AVATAR);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'referral_id', 'id');
    }

    /**
     * user chats.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }

    /**
     * @return \App\Models\Role
     */
    public function getRole()
    {
        return $this->roles()->first();
    }

    public function getRoleName()
    {
        return optional($this->getRole())->name;
    }

    public function hasRoleName($roleName)
    {
        return $this->getRoleName() == $roleName;
    }

    /**
     * check if user is manager.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRoleName(Role::ROLE_SUPERADMIN);
    }

    /**
     * check if user is manager.
     *
     * @return bool
     */
    public function isManager()
    {
        return $this->hasRoleName(Role::ROLE_MANAGER);
    }

    /**
     * check if user is consultant.
     *
     * @return bool
     */
    public function isConsultant()
    {
        return $this->hasRoleName(Role::ROLE_CONSULTANT);
    }

    /**
     * check if user is client.
     *
     * @return bool
     */
    public function isClient()
    {
        return $this->hasRoleName(Role::ROLE_CLIENT);
    }

    /**
     * check if user has email and password.
     * @return bool
     */
    public function isFilled()
    {
        return $this->email && $this->password;
    }

    /**
     * check if user has online status.
     *
     * @return bool
     */
    public function isOnline()
    {
        return \Cache::has(self::onlineKey($this->id));
    }

    public function getUnreadMessagesCount()
    {
        $user = $this;

        return Message::whereHas('chat', function ($query) use ($user) {
            $query->containsUser($user);
        })->where('user_id', '<>', $user->id)->unread()->count();
    }

    protected function setSearchBuilder(Builder $query, $field, $keywords, $or)
    {
        switch ($field) {
            case 'company_name':
                $query->scopeOrWhere('likeCompanyName', $keywords, $or);

                return true;
        }

        return false;
    }

    protected function setSortBuilder($query, $key, $value, $params)
    {
        switch ($key) {
            case 'company':
                $query->sortCompanyName($value);

                return true;
        }

        return false;
    }

    /**
     * get cache online key.
     *
     * @param $id
     * @return string
     */
    public static function onlineKey($id)
    {
        return self::CACHE_ONLINE_KEY.$id;
    }
}
