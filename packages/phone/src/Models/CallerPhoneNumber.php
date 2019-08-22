<?php

namespace Phone\Models;

use App\User;
use Phone\Enums\AutoAllocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CallerPhoneNumber
 * @package Phone\Models
 *
 */

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
     * @var null
     */
    public $suggested_allocation = null;

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
        if ($this->auto_allocation === AutoAllocation::AUTO_SUGGEST) {
            $transaction = $this->phoneTransaction()->with('accountPhoneNumber.phoneTransactions')->first();
            $accountPhoneNumber = $transaction->accountPhoneNumber;
            $this->suggested_allocation = optional($accountPhoneNumber->phoneTransactions->sortByDesc('id')->where('allocation_id', '!=', null)->first())->allocatedTo ?? null;
        }
    }

    /**
     * Load suggested allocation loads a suggestion, but does not attach it to the model
     * This method does the same, but with attaching
     */
    public function attachSuggestedAllocation(): void
    {
        if (!$this->suggested_allocation) {
            $this->loadSuggestedAllocation();
        }
        if (isset($this->suggested_allocation) && $this->suggested_allocation !== null) {
            $this->allocation_id = $this->suggested_allocation->id;
            $this->save();
        }
    }
}
