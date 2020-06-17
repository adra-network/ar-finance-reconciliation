<?php

namespace App;

use Hash;
use Carbon\Carbon;
use App\Traits\Cacheable;
use Account\Models\Account;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class User.
 * @property Carbon $logged_in_at
 */
class User extends Authenticatable
{
    use SoftDeletes, Notifiable, Cacheable;

    public $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
        'logged_in_at',
    ];

    protected $casts = [
        'email_notifications_enabled' => 'bool',
    ];

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'email_notifications_enabled',
        'logged_in_at',
    ];

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->cache('isAdmin', function () {
            return $this->roles()->where('role_id', 1)->first() !== null;
        });
    }

    /**
     * @return HasMany
     */
    public function accountPhoneNumbers(): HasMany
    {
        return $this->hasMany(AccountPhoneNumber::class);
    }

    /**
     * @return HasMany
     */
    public function callerPhoneNumbers(): HasMany
    {
        return $this->hasMany(CallerPhoneNumber::class);
    }

    /**
     * @param int $days
     * @return bool
     */
    public function hasNotLoggedInFor(int $days): bool
    {
        return $this->logged_in_at->diffInDays(now()) > $days;
    }
}
