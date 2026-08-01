<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KostPrice extends Model
{
    protected $fillable = [
        'kost_id',
        'period',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public static function periodLabels(): array
    {
        return Kost::rentPeriodLabels();
    }

    public static function allowedPeriods(): array
    {
        return array_keys(self::periodLabels());
    }
}
