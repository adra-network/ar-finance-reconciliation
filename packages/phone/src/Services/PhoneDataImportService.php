<?php

namespace Phone\Services;

use SpreadsheetReader;
use Phone\Enums\AutoAllocation;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;

class PhoneDataImportService
{
    const KEYS = [
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
     * @param string $storage_path
     * @return bool
     * @throws \Exception
     */
    public function importPhoneDataFromFile(string $storage_path): bool
    {
        $reader = new SpreadsheetReader($storage_path);

        $rowCounter = 0;
        $accountPhoneNumbers = AccountPhoneNumber::all()->pluck('id', 'phone_number');
        $callerPhoneNumbers = CallerPhoneNumber::with('phoneTransaction')->get();
        $transactions = [];

        $numbersToAutoAllocate = [];
        foreach ($callerPhoneNumbers as $cpn) {
            if ($cpn->auto_allocation === AutoAllocation::AUTO_ALLOCATE) {
                $numbersToAutoAllocate[$cpn->id] = $cpn;
            }
        }

        $callerPhoneNumbers = $callerPhoneNumbers->pluck('id', 'phone_number');

        foreach ($reader as $index => $stringRow) {
            if ($index === 0) {
                continue;
            }
            if ($index === 1) {
                continue;
            }

            $row = explode('|', implode('', $stringRow));

            $transaction = [];
            // We fill in all the fields into array, so keys would be identical for later PhoneTransaction::insert();
            foreach (self::KEYS as $keyNumber => $key) {
                $transaction[$key] = (isset($row[$keyNumber]) && $row[$keyNumber] != '') ? $row[$keyNumber] : null;
            }

            $transaction['total_charges'] = (float) $transaction['total_charges'];

            $accountPhoneNumber = isset($accountPhoneNumbers[$transaction['wireless_number']]) ? $accountPhoneNumbers[$transaction['wireless_number']] : null;
            if (is_null($accountPhoneNumber)) {
                $accountPhoneNumber = AccountPhoneNumber::create(['phone_number' => $transaction['wireless_number']])->id;
                $accountPhoneNumbers[$transaction['wireless_number']] = $accountPhoneNumber;
            }
            $transaction['account_phone_number_id'] = $accountPhoneNumber;

            $callerPhoneNumber = isset($callerPhoneNumbers[$transaction['number_called_to_from']]) ? $callerPhoneNumbers[$transaction['number_called_to_from']] : null;
            if (is_null($callerPhoneNumber) && ! is_null($transaction['number_called_to_from'])) {
                $callerPhoneNumber = CallerPhoneNumber::create(['phone_number' => $transaction['number_called_to_from']])->id;
                $callerPhoneNumbers[$transaction['number_called_to_from']] = $callerPhoneNumber;
                $transaction['caller_phone_number_id'] = $callerPhoneNumber;
            } else {
                if (! is_null($callerPhoneNumber)) {

                    //we auto allocate here
                    if (isset($numbersToAutoAllocate[$callerPhoneNumber])) {
                        /** @var CallerPhoneNumber $cpn */
                        $cpn = $numbersToAutoAllocate[$callerPhoneNumber];
                        $cpn->attachSuggestedAllocation();
                    }

                    $transaction['caller_phone_number_id'] = $callerPhoneNumber;
                } else {
                    $transaction['caller_phone_number_id'] = null;
                }
            }

            $transaction['created_at'] = $transaction['updated_at'] = now()->toDateTimeString();
            $transactions[] = $transaction;

            $rowCounter++;
            if ($rowCounter > 1000) { // We insert in batches by 1000
                PhoneTransaction::insert($transactions);

                $rowCounter = 0;
                $transactions = [];
            }
        }

        if (count($transactions) > 0) {
            PhoneTransaction::insert($transactions);
        }

        return true;
    }
}
