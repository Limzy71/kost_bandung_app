<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KostImage extends Model
{
    protected $fillable = ['kost_id', 'image_path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleting(function ($image) {
            if ($image->image_path) {
                Storage::delete($image->image_path);
            }
        });
    }

    /**
     * @return BelongsTo<Kost, $this>
     */
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }
}
