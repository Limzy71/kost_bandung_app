<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    protected $fillable = ['name', 'type', 'status', 'user_id', 'icon'];

    public function kosts(): BelongsToMany
    {
        return $this->belongsToMany(Kost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function resolveIcon(string $name): ?string
    {
        return config('bandung.facility_icons.' . $name);
    }
}