<?php

namespace Account\Controllers;

use Account\Models\Account;
use App\Http\Controllers\Controller;
use Account\Services\AccountPageTableService;
use Account\Services\BatchTableService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionsSummaryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Factory|View
     */
    public function __invoke(Request $request)
    {
        abort_unless(Gate::allows('transaction_access'), 403);

        // --- TABLE 1 ---
        $accounts = Account::all();

        $account_id    = $request->input('account_id', null);
        $selectedMonth = $request->input('month', null);

        if (!is_null($account_id) && !is_null($selectedMonth)) {
            $account = Account::find($account_id);
            $tables  = new AccountPageTableService($account, Carbon::parse($selectedMonth));

            $table1 = $tables->getTable1();
            $table2 = $tables->getTable2();

            $batchTable = (new BatchTableService())
                ->setClosingBalance($table1->monthlySummary->closing_balance ?? 0)
                ->showVariance()
                ->showOneAccount($account_id)
                ->getTableData();
        }

        return view('account::transactionsSummary.index', [
            'account'       => $account ?? null,
            'accounts'      => $accounts,
            'months'        => $this->getMonths(),
            'account_id'    => $account_id,
            'selectedMonth' => $selectedMonth,
            'table1'        => isset($table1) ? $table1 : null,
            'table2'        => isset($table2) ? $table2 : null,
            'batchTable'    => isset($batchTable) ? $batchTable : null,
        ]);
    }

    /**
     * @return array
     */
    private function getMonths(): array
    {
        $date      = now()->startOfMonth();
        $months    = [];
        $lastMonth = Carbon::parse('2017-01-01');
        do {
            $months[$date->format('m/Y')] = $date->format('Y-m');
            $date->subMonth();
        } while ($date->gte($lastMonth));

        return $months;
    }
}
