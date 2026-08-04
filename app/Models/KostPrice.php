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

    /**
     * @return BelongsTo<Kost, $this>
     */
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    /**
     * @return array<string, string>
     */
    public static function periodLabels(): array
    {
        return Kost::rentPeriodLabels();
    }

    /**
     * @return array<int, string>
     */
    public static function allowedPeriods(): array
    {
        return array_keys(self::periodLabels());
    }
}
