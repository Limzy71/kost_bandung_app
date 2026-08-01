<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kost extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::forceDeleting(function ($kost) {
            foreach ($kost->images as $image) {
                if ($image->image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(KostImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(KostImage::class)->where('is_primary', true);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(KostPrice::class);
    }

    public function pricesByPeriod(): array
    {
        return $this->prices()->get()->pluck('price', 'period')->map(fn ($p) => (string) $p)->all();
    }
}