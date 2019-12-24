<?php

namespace Account\Controllers;

use Account\Models\Account;
use Account\Models\AccountImport;
use Account\Services\AccountPageTableService;
use Account\Services\BatchTableService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

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
        $accounts = Account::get()->sortBy(function(Account $account) {
            return strtolower($account->getNameOnly());
        });
        $accountImports = AccountImport::get();

        $account_id = $request->input('account_id', null);
        $selectedImport = $request->input('import', null);


        $table1 = null;

        if (!is_null($account_id)) {
            $account = Account::findOrFail($account_id);
        }

        if (isset($account) && !is_null($selectedImport)) {
            $import = AccountImport::findOrFail($selectedImport);
            $tables = new AccountPageTableService($account, $import);

            $table1 = $tables->getTable1();
        }

        if (isset($account)) {
            $batchTable = (new BatchTableService())
                ->setClosingBalance(optional($table1)->monthlySummary->closing_balance ?? 0)
                ->showOneAccount($account_id)
                ->getTableData();
        }

        return view('account::transactionsSummary.index', [
            'account' => $account ?? null,
            'accounts' => $accounts,
            'account_id' => $account_id,
            'selectedImport' => $selectedImport,
            'table1' => $table1,
            'batchTable' => isset($batchTable) ? $batchTable : null,
            'accountImports' => $accountImports,
        ]);
    }
}
