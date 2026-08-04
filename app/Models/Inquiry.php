<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kost_id',
        'user_id',
        'name',
        'phone_number',
        'message',
        'owner_reply',
        'replied_at',
        'seeker_seen_reply_at',
        'status',
        'contacted_at',
    ];

    protected $casts = [
        'contacted_at'          => 'datetime',
        'replied_at'            => 'datetime',
        'seeker_seen_reply_at'  => 'datetime',
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
