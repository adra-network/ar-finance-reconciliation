<?php

namespace Phone\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneNumber extends Model
{
    use SoftDeletes;

    public $table = 'phone_numbers';

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
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PhoneTransaction::class);
    }

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
    public function allocated_to(): BelongsTo
    {
        return $this->belongsTo(Allocation::class);
    }
}
