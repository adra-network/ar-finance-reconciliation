<?php

namespace Account\Controllers;

use Illuminate\View\View;
use Account\Models\Account;
use Illuminate\Http\Request;
use Account\Models\AccountImport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\View\Factory;
use Account\Services\BatchTableService;
use Account\Services\AccountPageTableService;

class TransactionsSummaryController extends AccountBaseController
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
        $accountImports = AccountImport::get();

        $account_id = $request->input('account_id', null);
        $selectedImport = $request->input('import', null);

        if (! is_null($account_id) && ! is_null($selectedImport)) {
            $account = Account::find($account_id);
            $import = AccountImport::find($selectedImport);
            $tables = new AccountPageTableService($account, $import);

            $table1 = $tables->getTable1();
            $table2 = $tables->getTable2();

            $batchTable = (new BatchTableService())
                ->setClosingBalance($table1->monthlySummary->closing_balance ?? 0)
                ->showVariance()
                ->showOneAccount($account_id)
                ->getTableData();
        }

        return view('account::transactionsSummary.index', [
            'account' => $account ?? null,
            'accounts' => $accounts,
            'account_id' => $account_id,
            'selectedImport' => $selectedImport,
            'table1' => isset($table1) ? $table1 : null,
            'table2' => isset($table2) ? $table2 : null,
            'batchTable' => isset($batchTable) ? $batchTable : null,
            'accountImports' => $accountImports,
        ]);
    }
}
