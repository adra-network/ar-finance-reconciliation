<?php

namespace Phone\Controllers;

use SpreadsheetReader;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Phone\Models\PhoneTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;

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

//        $file     = $request->file('import_file');
//        $filename = 'import-'.$request->random_filename.'.'.$file->getClientOriginalExtension();
//        $file->storeAs('imports', $filename, 'local');
//
//        $path     = storage_path('app/public/' . $filename);
//
//        $reader = new SpreadsheetReader($path);
//
//        $insert = [];
//
//        foreach ($reader as $key => $row) {
//            $insert[] = $row;
//        }
//
//        foreach ($insert as $insert_item) {
//            if (array_key_exists(17, $insert_item) && $insert_item[17] > 0) {
//                PhoneTransaction::create(['phone_number' => $insert_item[3], 'total_charges' => $insert_item[17]]);
//            }
//        }

        dd('coming');

        return redirect()->route('phone.transactions.index')->withMessage(trans('global.import.imported_successfully'));
    }
}
