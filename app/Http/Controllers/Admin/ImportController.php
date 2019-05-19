<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportRequest;

class ImportController extends Controller
{
    public function create()
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        return view('admin.import.create');
    }

    public function store(StoreImportRequest $request)
    {
        $file = $request->file('import_file');
        $file->storeAs('imports', 'import-' . $request->random_filename . '.' . $file->getClientOriginalExtension(), 'local');

        return redirect()->route('admin.transactions.index')->withMessage(trans('global.import.imported_successfully'));
    }

}
