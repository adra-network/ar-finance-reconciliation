<?php

namespace Tests\Feature;

use App\Services\ExcelImportService;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ExcelImportTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_file_upload_page()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get('/admin/import/create');

        $response->assertSee('input type="file"');
    }

    public function test_file_uploaded()
    {
        Storage::fake();
        $user = User::find(1);
        $random_filename = time();

        $response = $this->actingAs($user)->post('/admin/import', [
            'import_file' => $file = UploadedFile::fake()->create('import.xlsx'),
            'random_filename' => $random_filename,
        ]);

        Storage::assertExists('imports/import-' . $random_filename . '.' . $file->getClientOriginalExtension());

        $response->assertRedirect('/admin/transactions');
    }

    public function test_file_importable()
    {
        // Get the file from storage
        // Try to import the data with Excel package
        $accounts = (new ExcelImportService())->parseMonthlyReportOfAccounts(storage_path('testing/Alfredo_April.xls'));

        // Assert has exactly 2 accounts
        $this->assertEquals($accounts->count(), 2);

        $account = $accounts->where('code', '01-1-0-00-0-0-000-14565')->first();

        // Check if first account code was read successfully
        $this->assertNotNull($account);

        // Check if account name was read successfully
        $this->assertEquals($account->name, '01-1-0-00-0-0-000-14565 (A/R - Alfredo)');

        // Check if account beginning balance was read successfully without $ signs and formatting
        $this->assertEquals($account->beginningBalance, 8731.08);

        // Check if account net change was read successfully
        $this->assertEquals($account->netChange, 150);

        // Check if account ending balance was read successfully
        $this->assertEquals($account->endingBalance, 8881.08);

        // Check if first account has transactions
        $this->assertNotEmpty($account->transactions);

        // Check if first transaction date is correctly formatted from m/d/Y to Y-m-d
        $this->assertEquals($account->transactions->first()->date->format('Y-m-d'), '2019-04-30');

        // Check if first transaction debit is correctly read
        $this->assertEquals($account->transactions->first()->debit, 150);

        // Check if first transaction debit is correctly read and changed from NULL to 0
        $this->assertEquals($account->transactions->first()->credit, 0);


        $second_account = $accounts->where('code', '01-1-0-00-0-0-000-14627')->first();

        // Check if second account code was read successfully
        $this->assertNotEmpty($second_account->transactions);

        // Check if second account net change was correctly set and formatted as negative value
        $this->assertEquals($second_account->netChange, -4356.48);
    }

}
