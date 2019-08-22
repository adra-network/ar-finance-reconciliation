<?php

namespace Phone\Models;

use Illuminate\Database\Eloquent\Model;
use Phone\DTO\TransactionListParameters;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PhoneTransaction extends Model
{
    public $table = 'phone_transactions';

    protected $dates = [
        'date',
    ];

    protected $casts = [
        'phone_number_id' => 'int',
    ];

    protected $fillable = [
        'phone_number_id',
        'section_id',
        'foundation_account_number',
        'foundation_account_name',
        'billing_account_number',
        'billing_account_name',
        'wireless_number',
        'market_cycle_end_date',
        'item',
        'date',
        'time',
        'rate_code',
        'rate_period',
        'feature',
        'type_code',
        'legend',
        'voice_data_indicator',
        'roaming_indicator',
        'total_charges',
        'originating_location',
        'number_called_to_from',
        'voice_called_to',
        'voice_in_out',
        'minutes_used',
        'airtime_charge',
        'ld_add_charge',
        'intl_tax',
        'day',
        'data_to_from',
        'data_type',
        'data_in_out',
        'data_usage_amount',
        'data_usage_measure',
        'video_share_rate_code',
        'video_share_to_from',
        'video_share_in_out',
        'video_share_domestic_usage_charges',
        'video_share_domestic_minutes',
        'video_share_international_roaming_location',
        'video_share_international_roaming_charges',
        'video_share_international_roaming_minutes',
        'vehicle_identification_number',
        'make',
        'model',
        'year',
        'trim',
        'comment',
        'allocation_id',
        'account_phone_number_id',
        'caller_phone_number_id',
    ];

    public function accountPhoneNumber()
    {
        return $this->belongsTo(AccountPhoneNumber::class, 'account_phone_number_id');
    }

    /**
     * @return BelongsTo
     */
    public function callerPhoneNumber(): BelongsTo
    {
        return $this->belongsTo(CallerPhoneNumber::class);
    }

    /**
     * @return BelongsTo
     */
    public function allocatedTo(): BelongsTo
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
    }

    /**
     * @param $grouping
     * @return mixed|string
     */
    public function getFieldByGrouping($grouping)
    {
        if ($grouping == TransactionListParameters::GROUP_BY_DATE) {
            return $this->date->format('m/d/Y').' '.Carbon::createFromFormat('H:i:s', $this->time)->format('H:i');
        } else {
            return $this->phone_number;
        }
    }
}
