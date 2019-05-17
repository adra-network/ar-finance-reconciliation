<?php

namespace Tests\Feature;

use App\Services\ExcelImportService;
use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ExcelImportTest extends TestCase
{

    public function testFileUploadPage()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get('/admin/import/create');

        $response->assertSee('input type="file"');
    }

    public function testFileUploaded()
    {
        Storage::fake();
        $user = User::find(1);

        $this->actingAs($user)->post('/admin/import', [
            'import_file' => $file = UploadedFile::fake()->create('import.xlsx')
        ]);

        Storage::assertExists('imports/' . $file->hashName());
    }

    public function testFileImportable()
    {
        // Get the file from storage
        // Try to import the data with Excel package
        $data = (new ExcelImportService())->import_account_month(storage_path('testing/Alfredo_April.xls'));

        // Assert result array is not empty
        $this->assertNotEmpty($data);

        // Assert B8 is Date
        $this->assertEquals('Date', $data[7][1]);

        // Assert B10 is not empty - at least 1 account
        $this->assertNotNull($data[9][1]);

        // Assert has exactly 2 accounts

        // Assert first account beginning balance + net change = ending balance
        // Assert first account debit + credit = net change
    }

}
