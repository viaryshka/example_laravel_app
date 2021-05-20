<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Attachment extends Model
{
    const TYPE_AVATAR = 'avatar';
    const TYPE_UPLOADED_FOR_PROPERTY = 'uploaded_for_property';
    const TYPE_UPLOADED_FOR_MESSAGE = 'uploaded_for_message';

    public static $allowedImages = ['png', 'jpg', 'jpeg'];
    public static $allowedDocuments = ['txt', 'doc', 'docx', 'xls', 'xlsx', 'pdf'];

    protected $fillable = [
        'src',
        'name',
        'type',
        'sort',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(fn ($query) => $query->orderBy('sort'));
    }

    public function attachable()
    {
        return $this->morphTo();
    }

    public function getUrl()
    {
        return Storage::disk('public')->url($this->src);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to get by property id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param $propertyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPropertyId($query, $propertyId)
    {
        return $query->where('attachable_type', Property::class)
                     ->where('attachable_id', $propertyId);
    }

    /**
     * Scope a query to get by user id and uploaded for property.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUploadedForProperty($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->whereNull('attachable_type')
                     ->where('type', self::TYPE_UPLOADED_FOR_PROPERTY);
    }

    /**
     * return allowed images extensions.
     * @return string[]
     */
    public static function allowedImages()
    {
        return self::$allowedImages;
    }

    /**
     * return allowed files extensions.
     * @return string[]
     */
    public static function allowedDocuments()
    {
        return self::$allowedDocuments;
    }

    /**
     * return allowed images and files extensions.
     * @return array
     */
    public static function allowedAttachments()
    {
        return array_merge(self::allowedImages(), self::allowedDocuments());
    }
}
