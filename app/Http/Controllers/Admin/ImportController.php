<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function create()
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        return view('admin.import.create');
    }

    public function store(Request $request)
    {
        $request->file('import_file')->store('imports', 'local');
    }

}
