<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KostConversation extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_ARCHIVED_BY_OWNER = 'archived_by_owner';

    public const STATUS_ARCHIVED_BY_SEEKER = 'archived_by_seeker';

    protected $fillable = [
        'kost_id',
        'seeker_id',
        'status',
    ];

    /**
     * @return BelongsTo<Kost, $this>
     */
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function seeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seeker_id');
    }

    /**
     * @return HasMany<KostMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(KostMessage::class, 'conversation_id')->orderBy('created_at');
    }

    /**
     * Pesan terbaru untuk pratinjau daftar percakapan (one-of-many).
     *
     * @return HasOne<KostMessage, $this>
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(KostMessage::class, 'conversation_id')->latestOfMany();
    }

    public function isHiddenForOwner(): bool
    {
        return $this->status === self::STATUS_ARCHIVED_BY_OWNER;
    }

    public function isHiddenForSeeker(): bool
    {
        return $this->status === self::STATUS_ARCHIVED_BY_SEEKER;
    }
}
