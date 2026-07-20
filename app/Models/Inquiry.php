<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kost_id',
        'user_id',
        'name',
        'phone_number',
        'message',
        'status',
        'contacted_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
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