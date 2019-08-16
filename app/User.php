<?php

namespace App;

use Hash;
use App\Traits\Cacheable;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
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
}
