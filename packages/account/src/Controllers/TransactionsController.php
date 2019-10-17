<?php

namespace Account\Controllers;

use Account\Services\BatchTableService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransactionsController extends AccountBaseController
{
    public function index(Request $request)
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        $dateFilter = $request->input('date_filter', null);
        if ($dateFilter) {
            [$d1, $d2] = explode(' - ', $dateFilter);
            $d1 = Carbon::parse($d1)->startOfMonth();
            $d2 = Carbon::parse($d2)->endOfMonth();
        } else {
            $d1 = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $d2 = now()->subMonth()->endOfMonth()->format('Y-m-d');
            $d = $d1 . ' - ' . $d2;

            return redirect()->route('account.transactions.index', ['date_filter' => $d]);
        }

        $batchTableService = new BatchTableService();
        $batchTable = $batchTableService->getTableData();

        $batchTableService->limitByDateRange($d1, $d2);
        $batchTableWithPreviousMonths = $batchTableService->getTableData();

        return view('account::transactions.index', [
            'batchTable' => $batchTable,
            'batchTableWithPreviousMonths' => $batchTableWithPreviousMonths,
        ]);
    }
}
