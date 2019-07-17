<?php

namespace Phone\Models;

use Faker\Provider\PhoneNumber;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function phone_number()
    {
        return $this->belongsTo(PhoneNumber::class, 'phone_number_id');
    }
}
