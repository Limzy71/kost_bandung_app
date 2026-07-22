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

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'gender_type',
        'price_monthly',
        'address',
        'district',
        'latitude',
        'longitude',
        'is_available',
        'status',
        'total_rooms',
        'available_rooms',
        'additional_rules_note',
        'boosted_at',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'total_rooms' => 'integer',
        'available_rooms' => 'integer',
        'price_monthly' => 'decimal:2',
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
}