<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoostReminder extends Model
{
    protected $fillable = [
        'kost_id',
        'user_id',
        'reminder_type',
        'boost_expires_at',
        'sent_at',
    ];

    protected $casts = [
        'boost_expires_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Kost, $this>
     */
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }
}
