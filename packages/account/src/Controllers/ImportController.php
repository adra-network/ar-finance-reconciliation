<?php

namespace Account\Controllers;

use App\User;
use Illuminate\View\View;
use Account\Models\AccountImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Account\Requests\StoreImportRequest;
use Account\Services\ExcelImportService;

class ImportController extends AccountBaseController
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
        $request->validate([
            'title' => ['required'],
        ]);

        /** @var User $user */
        $user = auth()->user();

        $file = $request->file('import_file');
        $filename = sprintf('import-%s.%s', time(), $file->getClientOriginalExtension());
        $file->storeAs('imports', $filename, 'local');

        $title = $request->input('title');

        DB::beginTransaction();

        try {
            $accountImport = new AccountImport([
                'user_id' => $user->id,
                'title' => $title,
                'filename' => $filename,
            ]);
            $accountImport->save();

            $excelImportService = new ExcelImportService();
            $accounts = $excelImportService->parseMonthlyReportOfAccounts(storage_path('app/imports/'.$filename));
            $excelImportService->saveParsedDataToDatabase($accounts, $accountImport);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('account.transactions.index')->withMessage(trans('global.import.imported_successfully'));
    }
}
