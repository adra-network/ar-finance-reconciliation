<?php

namespace Tests\Feature;

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

}
