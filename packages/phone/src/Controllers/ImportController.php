<?php

namespace Phone\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Phone\Services\PhoneDataImportService;

class ImportController extends Controller
{
    /**
     * @return View
     */
    public function create(): View
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        return view('phone::import.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        $file = $request->file('import_file');
        $filename = 'import-'.$request->random_filename.'.'.$file->getClientOriginalExtension();
        $file->storeAs('imports', $filename, 'local');

        $phoneDataImportService = new PhoneDataImportService();
        $phoneDataImportService->importPhoneDataFromFile(storage_path('app/imports/'.$filename));

        return redirect()->route('phone.transactions.index')->withMessage(trans('global.import.imported_successfully'));
    }
}
