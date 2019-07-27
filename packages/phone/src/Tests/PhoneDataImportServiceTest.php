<?php

namespace Tests\Unit\Services;

use App\User;
use Tests\TestCase;
use Phone\Models\PhoneNumber;
use Phone\Models\PhoneTransaction;
use Phone\Services\PhoneDataImportService;

class PhoneDataImportServiceTest extends TestCase
{
    public function test_phone_data_import()
    {
        $service = new PhoneDataImportService();
        $service->importPhoneDataFromFile(storage_path('testing/phone_data_for_testing_small.csv'));

        $transactions = PhoneTransaction::get();
        $this->assertEquals(3498, $transactions->count());
        $this->assertEquals(46, PhoneNumber::get()->count());
    }

    public function test_phone_data_import_as_admin()
    {
        $user = User::find(1);
        auth()->login($user);

        $service = new PhoneDataImportService();
        $service->importPhoneDataFromFile(storage_path('testing/phone_data_for_testing_small.csv'));

        $transaction = PhoneTransaction::first();
        $phone_number = PhoneNumber::find($transaction->phone_number_id);

        $this->assertEquals(null, $phone_number->user_id);
    }

    public function test_phone_data_import_as_non_admin()
    {
        $user = User::find(2);
        auth()->login($user);

        $service = new PhoneDataImportService();
        $service->importPhoneDataFromFile(storage_path('testing/phone_data_for_testing_small.csv'));

        $transaction = PhoneTransaction::first();
        $phone_number = PhoneNumber::find($transaction->phone_number_id);

        $this->assertEquals($user->id, $phone_number->user_id);
    }
}
