<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoostTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'kost_id',
        'amount',
        'status',
        'midtrans_status',
        'payment_type',
        'snap_token',
        'midtrans_response',
        'paid_at',
    ];

    protected $casts = [
        'midtrans_response' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'integer',
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
