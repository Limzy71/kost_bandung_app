<?php

namespace App\Models;

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
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $identity_verified_at
 * @property string|null $identity_doc_path
 * @property string $identity_verification_status
 * @property string|null $identity_rejection_note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $avatar_url
 */
#[Fillable(['name', 'email', 'avatar', 'phone_number', 'business_name', 'password', 'role', 'identity_doc_path', 'identity_verification_status', 'identity_verified_at', 'identity_rejection_note'])]
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
            'identity_verified_at' => 'datetime',
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
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->avatar ? Storage::disk(config('filesystems.default'))->url($this->avatar) : null,
        );
    }

    /**
     * Delete the avatar file from storage if present.
     */
    public function deleteAvatarFile(): void
    {
        if ($this->avatar) {
            Storage::disk(config('filesystems.default'))->delete($this->avatar);
        }
    }

    /**
     * Delete the identity document file from storage if present.
     */
    public function deleteIdentityDocumentFile(): void
    {
        if ($this->identity_doc_path) {
            Storage::disk(config('filesystems.default'))->delete($this->identity_doc_path);
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
     * @return HasMany<Inquiry, $this>
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }
}
