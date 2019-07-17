<?php

namespace Account\Controllers;

use App\Http\Controllers\Controller;
use Account\Requests\StoreImportRequest;
use Account\Services\ExcelImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ImportController extends Controller
{
    /**
     * @return View
     */
    public function create(): View
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        return view('account::import.create');
    }

    /**
     * @param StoreImportRequest $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(StoreImportRequest $request): RedirectResponse
    {
        $file     = $request->file('import_file');
        $filename = 'import-'.$request->random_filename.'.'.$file->getClientOriginalExtension();
        $file->storeAs('imports', $filename, 'local');

        $excelImportService = new ExcelImportService();
        $accounts           = $excelImportService->parseMonthlyReportOfAccounts(storage_path('app/imports/'.$filename));
        $excelImportService->saveParsedDataToDatabase($accounts);

        return redirect()->route('account.transactions.index')->withMessage(trans('global.import.imported_successfully'));
    }
}
