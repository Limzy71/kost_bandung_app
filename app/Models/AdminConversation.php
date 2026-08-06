<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminConversation extends Model
{
    use SoftDeletes;

    public const CATEGORIES = ['komplain', 'pertanyaan', 'masukan', 'lainnya'];

    protected $fillable = [
        'user_id',
        'sender_role',
        'category',
        'status',
        'closed_reason',
        'awaiting_reply_at',
        'closed_at',
    ];

    protected $casts = [
        'awaiting_reply_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<AdminMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AdminMessage::class, 'conversation_id')->orderBy('created_at');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'komplain' => 'Komplain',
            'pertanyaan' => 'Pertanyaan',
            'masukan' => 'Masukan / Saran',
            default => 'Lainnya',
        };
    }

    /**
     * Menutup percakapan yang belum dibalas admin dalam 1x24 jam sejak pesan
     * terakhir user (diukur dari awaiting_reply_at).
     */
    public static function expireStale(): int
    {
        $deadline = now()->subHours(24);

        return self::query()
            ->where('status', 'open')
            ->whereNotNull('awaiting_reply_at')
            ->where('awaiting_reply_at', '<', $deadline)
            ->update([
                'status' => 'closed',
                'closed_reason' => 'expired',
                'closed_at' => now(),
                'awaiting_reply_at' => null,
            ]);
    }

    /**
     * Menghapus permanen percakapan yang sudah di-soft-delete melewati masa
     * retensi 30 hari (pesan ikut terhapus via cascade FK).
     */
    public static function pruneSoftDeleted(): int
    {
        $deadline = now()->subDays(30);

        return (int) self::query()
            ->onlyTrashed()
            ->where('deleted_at', '<', $deadline)
            ->forceDelete();
    }
}
