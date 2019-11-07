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
            [$dateFrom, $dateTo] = explode(' - ', $dateFilter);
            $dateFrom = Carbon::parse($dateFrom)->startOfDay();
            $dateTo = Carbon::parse($dateTo)->endOfDay();
        } else {
            $dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
            $d = $dateFrom . ' - ' . $dateTo;

            return redirect()->route('account.transactions.index', ['date_filter' => $d]);
        }

        $showFullyReconciled = $request->query('showReconciled', false);
        $dateFilter2 = $request->input('date_filter2', null);
        $dateFrom2 = null;
        $dateTo2 = null;
        if ($dateFilter2 && $showFullyReconciled) {
            [$dateFrom2, $dateTo2] = explode(' - ', $dateFilter2);
            $dateFrom2 = Carbon::parse($dateFrom2)->startOfDay();
            $dateTo2 = Carbon::parse($dateTo2)->endOfDay();
        }

        $showVariance = $request->query('showVariance', null);

        $batchTableService = new BatchTableService();
        $batchTable = $batchTableService->getTableData();

        return view('account::transactions.index', [
            'showFullyReconciled' => $showFullyReconciled,
            'dateFilter' => [$dateFrom, $dateTo],
            'dateFilter2' => [$dateFrom2, $dateTo2],
            'batchTable' => $batchTable,
        ]);
    }
}
