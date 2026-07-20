<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KostImage extends Model
{
    protected $fillable = ['kost_id', 'image_path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }
}