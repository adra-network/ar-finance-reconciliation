<?php

namespace Tests\Feature;

use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Models\AccountImport;
use Account\Models\MonthlySummary;
use Account\Services\ExcelImportService;

class ExcelImportServiceTest extends TestCase
{
    /**
     * Importing two accounts from excel but one has existed before the import
     * Testing that it will save only one and not two
     * Also testing account months and transactions imported successfully.
     *
     * @group shouldRun
     */
    public function test_two_accounts_imported_one_saved()
    {
        // Prepare the database - delete all accounts and add only one, the first one from excel
        factory(Account::class)->create([
            'code' => '01-1-0-00-0-0-000-14565',
            'name' => '01-1-0-00-0-0-000-14565 (A/R - Alfredo)',
        ]);

        // Get the file from storage
        // Try to import the data with Excel package
        $excelImportService = new ExcelImportService();
        $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('testing/May2019-Employee_AR-Import_Rec-V1_(1).csv'));
        $accountImport = factory(AccountImport::class)->create();

        $excelImportService->saveParsedDataToDatabase($accounts, $accountImport);

        // Check if there are two accounts in total, and not three
        $this->assertEquals(82, Account::count());

        // Check if latest account has code from Excel - 01-1-0-00-0-0-000-14627
        $this->assertEquals('01-1-0-00-0-0-000-14204', Account::find(2)->code);

        // Check if latest account has month imported with beginning_balance and endint_balance from excel
        $this->assertDatabaseHas('account_period_summaries', [
            'account_id'        => 2,
            'account_import_id' => 1,
            'date_from'    => '2019-05-01',
            'date_to'    => '2019-05-31',
        ]);

        // Check if there are 10 transactions for second account in April 2019
        $this->assertEquals(3, Transaction::where('account_id', 2)->whereYear('transaction_date', '2019')->whereMonth('transaction_date', '05')->count());

        // Check if random transaction from Excel was saved into the database
        $this->assertDatabaseHas('account_transactions', [
            'account_id'       => 2,
            'transaction_date' => '2019-05-15',
            'code'             => '87214-27',
            'debit_amount'     => 2350.00,
            'credit_amount'    => null,
        ]);
    }

    /**
     * Test uploading/importing same Excel file twice, and test if there is nothing new imported on the second time.
     *
     * @group shouldRun
     */
    public function test_reimporting_same_month_twice()
    {
        ini_set('memory_limit', -1);
        // Get the file from storage
        // Try to import the data with Excel package
        $excelImportService = new ExcelImportService();

        $accountImport = factory(AccountImport::class)->create();

        $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('testing/May2019-Employee_AR-Import_Rec-V1_(1).csv'));
        $excelImportService->saveParsedDataToDatabase($accounts, $accountImport);

        // Calling it twice
        $excelImportService = new ExcelImportService();
        $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('testing/May2019-Employee_AR-Import_Rec-V1_(1).csv'));
        $excelImportService->saveParsedDataToDatabase($accounts, $accountImport);

        // Check if there are two accounts in total
        $this->assertEquals(81, Account::count());

        // Check if there are two account months in total
        $this->assertEquals(81, MonthlySummary::count());

        // Check if there are 11 transactions in total, as per Excel
        $this->assertEquals(286, Transaction::count());
    }
}
