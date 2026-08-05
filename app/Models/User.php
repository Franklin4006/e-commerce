<?php

namespace App\Models;

use App\Enums\AdminRole;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_otp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'role' => AdminRole::class,
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Generate a fresh 6-digit email verification OTP, store it hashed, and return the plain code.
     */
    public function generateEmailVerificationOtp(): string
    {
        $code = (string) random_int(100000, 999999);

        $this->forceFill([
            'email_otp' => Hash::make($code),
            'email_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        return $code;
    }

    /**
     * Verify a submitted OTP code and, if valid, mark the email as verified.
     */
    public function verifyEmailOtp(string $code): bool
    {
        if (! $this->email_otp || ! $this->email_otp_expires_at || $this->email_otp_expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($code, $this->email_otp)) {
            return false;
        }

        $this->forceFill([
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ]);

        if (! $this->hasVerifiedEmail()) {
            $this->markEmailAsVerified();
        } else {
            $this->save();
        }

        return true;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
