<?php

namespace Phone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneNumber extends Model
{
    use SoftDeletes;

    public $table = 'phone_numbers';

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $fillable = ['phone_number'];

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PhoneTransaction::class);
    }
}
