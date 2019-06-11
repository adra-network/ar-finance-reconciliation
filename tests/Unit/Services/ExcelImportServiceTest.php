<?php

namespace Tests\Feature;

use App\Account;
use App\AccountMonthlySummary;
use App\AccountTransaction;
use App\Services\ExcelImportService;
use Tests\TestCase;

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
        $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('testing/Alfredo_April.xls'));

        $excelImportService->saveParsedDataToDatabase($accounts);

        // Check if there are two accounts in total, and not three
        $this->assertEquals(2, Account::count());

        // Check if latest account has code from Excel - 01-1-0-00-0-0-000-14627
        $this->assertDatabaseHas('accounts', [
            'id'   => 2,
            'code' => '01-1-0-00-0-0-000-14627',
            'name' => '01-1-0-00-0-0-000-14627 (A/R - Miscellaneous Employee)',
        ]);

        // Check if latest account has month imported with beginning_balance and endint_balance from excel
        $this->assertDatabaseHas('account_monthly_summaries', [
            'account_id'        => 2,
            'month_date'        => '2019-04-01',
            'beginning_balance' => 4356.48,
            'ending_balance'    => 0,
        ]);

        // Check if there are 10 transactions for second account in April 2019
        $this->assertEquals(10, AccountTransaction::where('account_id', 2)->whereYear('transaction_date', '2019')->whereMonth('transaction_date', '04')->count());

        // Check if random transaction from Excel was saved into the database
        $this->assertDatabaseHas('account_transactions', [
            'account_id'       => 2,
            'transaction_date' => '2019-04-01',
            'code'             => '87155-1',
            'debit_amount'     => null,
            'credit_amount'    => 50,
        ]);
    }

    /**
     * Test uploading/importing same Excel file twice, and test if there is nothing new imported on the second time.
     *
     * @group shouldRun
     */
    public function test_reimporting_same_month_twice()
    {
        // Get the file from storage
        // Try to import the data with Excel package
        $excelImportService = new ExcelImportService();

        $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('testing/Alfredo_April.xls'));
        $excelImportService->saveParsedDataToDatabase($accounts);

        // Calling it twice
        $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('testing/Alfredo_April.xls'));
        $excelImportService->saveParsedDataToDatabase($accounts);

        // Check if there are two accounts in total
        $this->assertEquals(2, Account::count());

        // Check if there are two account months in total
        $this->assertEquals(2, AccountMonthlySummary::count());

        // Check if there are 11 transactions in total, as per Excel
        $this->assertEquals(11, AccountTransaction::count());
    }
}
