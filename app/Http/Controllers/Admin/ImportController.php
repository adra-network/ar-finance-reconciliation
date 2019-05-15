<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    public function create()
    {
        abort_unless(\Gate::allows('transaction_access'), 403);

        return view('admin.import.create');
    }

}
