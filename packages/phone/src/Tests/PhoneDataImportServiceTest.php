<?php

namespace Tests\Unit\Services;

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
}
