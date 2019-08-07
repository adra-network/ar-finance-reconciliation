<?php

namespace Phone\Models;

use Phone\Enums\ChargeTo;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    public $table = 'allocations';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'charge_to',
        'account_number',
    ];

    protected static function boot()
    {
        parent::boot();
        parent::updating(function (self $allocation) {
            if ($allocation->charge_to !== ChargeTo::ACCOUNT) {
                $allocation->account_number = null;
            }
        });
    }
}
