<?php

namespace Tests\Feature;

use App\Account;
use App\Services\ExcelImportService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountsImportTest extends TestCase
{

    use DatabaseMigrations;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // Importing two accounts from excel but one has existed before the import
    // Testing that it will save only one and not two
    public function test_two_accounts_imported_one_saved()
    {
        // Prepare the database - delete all accounts and add only one, the first one from excel
        Account::create([
            'code' => '01-1-0-00-0-0-000-14565'
        ]);

        // Get the file from storage
        // Try to import the data with Excel package
        $excelImportService = new ExcelImportService();
        $accounts = $excelImportService->import_account_month(storage_path('testing/Alfredo_April.xls'));

        $excelImportService->save_accounts_and_transactions($accounts);

        // Check if there are two accounts in total, and not three
        $this->assertEquals(2, Account::count());

        // Check if latest account has code from Excel - 01-1-0-00-0-0-000-14627
        $this->assertDatabaseHas('accounts', ['id' => 2, 'code' => '01-1-0-00-0-0-000-14627']);
    }

}
