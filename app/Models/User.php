<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $google_id
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property CarbonImmutable|null $identity_verified_at
 * @property string|null $identity_doc_path
 * @property string $identity_verification_status
 * @property string|null $identity_rejection_note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $avatar_url
 */
#[Fillable(['name', 'email', 'email_verified_at', 'google_id', 'avatar', 'phone_number', 'business_name', 'password', 'role', 'identity_doc_path', 'identity_verification_status', 'identity_verified_at', 'identity_rejection_note'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'identity_verified_at' => 'immutable_datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check whether the owner's identity (KTP) has been verified by admin.
     */
    public function isIdentityVerified(): bool
    {
        return $this->identity_verification_status === 'verified';
    }

    /**
     * Human-readable label for the identity verification status.
     */
    public function identityStatusLabel(): string
    {
        return [
            'unverified' => 'Belum Diverifikasi',
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Identitas Terverifikasi',
            'rejected' => 'Dokumen Ditolak',
        ][$this->identity_verification_status] ?? 'Belum Diverifikasi';
    }

    /**
     * Get the user's avatar URL or null.
     *
     * @return Attribute<?string, never>
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->avatar) {
                    return null;
                }

                if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                    return $this->avatar;
                }

                return Storage::disk(config('filesystems.default'))->url($this->avatar);
            },
        );
    }

    /**
     * Delete the avatar file from storage if present.
     */
    public function deleteAvatarFile(): void
    {
        if ($this->avatar && ! str_starts_with($this->avatar, 'http://') && ! str_starts_with($this->avatar, 'https://')) {
            Storage::disk(config('filesystems.default'))->delete($this->avatar);
        }
    }

    /**
     * Delete the identity document file from storage if present.
     */
    public function deleteIdentityDocumentFile(): void
    {
        if ($this->identity_doc_path) {
            Storage::disk('verification_docs')->delete($this->identity_doc_path);
        }
    }

    /**
     * Permanently remove every file owned by this user from storage:
     * avatar, identity document, and all kosts' ownership documents and images.
     * Call before deleting the user record so no orphan files remain.
     */
    public function purgeAllDataFiles(): void
    {
        $this->deleteAvatarFile();
        $this->deleteIdentityDocumentFile();

        foreach (Kost::withTrashed()->where('user_id', $this->id)->get() as $kost) {
            $kost->deleteOwnershipDocumentFile();
            $kost->forceDelete();
        }
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * @return HasMany<Kost, $this>
     */
    public function kosts(): HasMany
    {
        return $this->hasMany(Kost::class);
    }

    /**
     * Percakapan chat kost di mana user berperan sebagai pencari kost.
     *
     * @return HasMany<KostConversation, $this>
     */
    public function kostConversations(): HasMany
    {
        return $this->hasMany(KostConversation::class, 'seeker_id');
    }
}
