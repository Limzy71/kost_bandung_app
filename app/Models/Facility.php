<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    protected $fillable = ['name', 'type', 'status', 'user_id'];

    public function kosts(): BelongsToMany
    {
        return $this->belongsToMany(Kost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}