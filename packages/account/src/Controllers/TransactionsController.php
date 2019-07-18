<?php

namespace Account\Controllers;

use Account\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Account\Services\BatchTableService;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        $withPreviousMonths = $request->query('withPreviousMonths', '0');

        $batchTableService = new BatchTableService();
        $batchTableService->setWithPreviousMonths((int) $withPreviousMonths);
        $batchTable = $batchTableService->getTableData();

        return view('account::transactions.index', compact('batchTable'));
    }
}
