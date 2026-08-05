<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Kost extends Model
{
    use SoftDeletes;

    public const RENT_PERIODS = ['daily', 'weekly', 'monthly', 'three_monthly', 'six_monthly', 'yearly'];

    protected static function booted()
    {
        static::forceDeleting(function ($kost) {
            foreach ($kost->images as $image) {
                if ($image->image_path) {
                    Storage::disk(config('filesystems.default'))->delete($image->image_path);
                }
            }
        });
    }

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'gender_type',
        'price_monthly',
        'rent_period',
        'price_deposit',
        'include_utilities',
        'address',
        'district',
        'latitude',
        'longitude',
        'is_available',
        'status',
        'total_rooms',
        'available_rooms',
        'whatsapp_contact',
        'nearby_landmarks',
        'additional_rules_note',
        'boosted_at',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'total_rooms' => 'integer',
        'available_rooms' => 'integer',
        'price_monthly' => 'decimal:2',
        'price_deposit' => 'decimal:2',
        'include_utilities' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'boosted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<KostImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(KostImage::class);
    }

    /**
     * @return HasOne<KostImage, $this>
     */
    public function primaryImage()
    {
        return $this->hasOne(KostImage::class)->where('is_primary', true);
    }

    /**
     * @return BelongsToMany<Facility, $this>
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    /**
     * @return BelongsToMany<Rule, $this>
     */
    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class);
    }

    /**
     * @return HasMany<Inquiry, $this>
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * @return HasMany<KostPrice, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(KostPrice::class);
    }

    /**
     * @return array<string, string>
     */
    public function pricesByPeriod(): array
    {
        return $this->prices()->pluck('price', 'period')->map(fn ($p) => (string) $p)->all();
    }

    public function getMonthlyEquivalentAttribute(): float
    {
        $period = (string) ($this->rent_period ?? 'monthly');

        $factor = match ($period) {
            'daily' => 30,
            'weekly' => 4.333,
            'three_monthly' => 1 / 3,
            'six_monthly' => 1 / 6,
            'yearly' => 1 / 12,
            default => 1,
        };

        return round((float) $this->price_monthly * $factor);
    }

    /**
     * @return array<string, string>
     */
    public static function rentPeriodLabels(): array
    {
        return [
            'daily' => 'Per Hari',
            'weekly' => 'Per Minggu',
            'monthly' => 'Per Bulan',
            'three_monthly' => 'Per 3 Bulan',
            'six_monthly' => 'Per 6 Bulan',
            'yearly' => 'Per Tahun',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function allowedRentPeriods(): array
    {
        return self::RENT_PERIODS;
    }

    /**
     * @return array<string, int>
     */
    public static function rentPeriodOrder(): array
    {
        return [
            'daily' => 1,
            'weekly' => 2,
            'monthly' => 3,
            'three_monthly' => 4,
            'six_monthly' => 5,
            'yearly' => 6,
        ];
    }

    public static function rentPeriodUnit(?string $period): string
    {
        return [
            'daily' => '/hari',
            'weekly' => '/minggu',
            'monthly' => '/bln',
            'three_monthly' => '/3 bln',
            'six_monthly' => '/6 bln',
            'yearly' => '/tahun',
        ][$period ?? ''] ?? '/bln';
    }
}
