<?php

namespace Phone\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountPhoneNumber extends Model
{
    use SoftDeletes;

    protected $fillable = ['phone_number', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function phoneTransactions(): HasMany
    {
        return $this->hasMany(PhoneTransaction::class, 'account_phone_number_id');
    }
}
