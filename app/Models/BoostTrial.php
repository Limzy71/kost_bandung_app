<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoostTrial extends Model
{
    protected $fillable = [
        'user_id',
        'kost_id',
        'owner_email',
        'owner_phone',
        'device_fingerprint_hash',
        'claimed_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
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
