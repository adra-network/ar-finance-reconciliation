<?php

namespace Phone\Models;

use App\User;
use Phone\Enums\AutoAllocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallerPhoneNumber extends Model
{
    use SoftDeletes;

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'remember' => 'bool',
    ];

    protected $fillable = [
        'phone_number',
        'user_id',
        'name',
        'auto_allocation',
        'remember',
        'charge_to',
        'account_number',
        'comment',
        'allocation_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function allocatedTo(): BelongsTo
    {
        return $this->belongsTo(Allocation::class);
    }

    /**
     * @return HasOne
     */
    public function phoneTransaction(): HasOne
    {
        return $this->hasOne(PhoneTransaction::class);
    }

    /**
     * Loads a suggested allocation into suggested_allocation attribute.
     */
    public function loadSuggestedAllocation(): void
    {
        if ($this->auto_allocation !== AutoAllocation::AUTO_SUGGEST) {
            $this->attributes['suggested_allocation'] = null;
        } else {
            $transaction = $this->phoneTransaction()->with('accountPhoneNumber.phoneTransactions')->first();
            $accountPhoneNumber = $transaction->accountPhoneNumber;
            $this->attributes['suggested_allocation'] = optional($accountPhoneNumber->phoneTransactions->sortByDesc('id')->where('allocation_id', '!=', null)->first())->allocatedTo ?? null;
        }
    }
}
