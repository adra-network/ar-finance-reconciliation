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

        $showZeroVariance = $request->query('showZeroVariance', null);

        $batchTableService = new BatchTableService();
        if (request()->input('account_id')) {
            $batchTableService->showOneAccount(request()->input('account_id'));
        }

        $pageNumber = $request->input('page', 1);
        $entriesPerPage = 10;
        $batchTable = $batchTableService->getTableData($showZeroVariance, $pageNumber, $entriesPerPage);

        $queryParameters = $request->query();
        unset($queryParameters['page']);
        $queryParams = '';
        foreach ($queryParameters as $key => $value) {
            $queryParams .= $key . '=' . $value . '&';
        }
        $queryParams = substr($queryParams, 0, -1); // remove last & symbol

        return view('account::transactions.index', [
            'showFullyReconciled' => $showFullyReconciled,
            'dateFilter' => [$dateFrom, $dateTo],
            'dateFilter2' => [$dateFrom2, $dateTo2],
            'batchTable' => $batchTable,
            'showZeroVariance' => $showZeroVariance,
            'accountsPerPage' => $entriesPerPage,
            'pageNumber' => $pageNumber,
            'queryParams' => $queryParams
        ]);
    }
}
