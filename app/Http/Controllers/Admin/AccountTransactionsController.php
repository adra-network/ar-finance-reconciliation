<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountMonthlySummary;
use App\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Services\BatchTableService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountTransactionsController extends Controller
{

    /** @var  Request */
    protected $request;

    /** @var  object|null */
    protected $table1 = null;

    /** @var  object|null */
    protected $table2 = null;

    /** @var  string */
    protected $selectedMonth;

    /** @var  int */
    protected $account_id;

    /** @var  object */
    protected $batchTable;

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function __invoke(Request $request)
    {
        $this->request = $request;

        abort_unless(\Gate::allows('transaction_access'), 403);

        // --- TABLE 1 ---
        $accounts = Account::all();

        $this->account_id = $request->input('account_id', false);
        $this->selectedMonth = $request->input('month', false);

        if ($this->account_id && $this->selectedMonth) {
            $account = Account::find($this->account_id);
            $this->loadTable1();
            $this->loadTable2();

            $this->batchTable = (new BatchTableService())
                ->setClosingBalance($this->table1->monthlySummary->closing_balance ?? 0)
                ->showVariance()
                ->showOneAccount($this->account_id)
                ->getTableData();
        }

        return view('admin.accounts.transactions.index', [
            'account' => $account ?? null,
            'accounts' => $accounts,
            'months' => $this->getMonths(),
            'account_id' => $this->account_id,
            'selectedMonth' => $this->selectedMonth,
            'table1' => $this->table1,
            'table2' => $this->table2,
            'batchTable' => $this->batchTable,
        ]);
    }

    /**
     * @return array
     */
    private function getMonths(): array
    {
        $date = now()->startOfMonth();
        $months = [];
        $lastMonth = Carbon::parse('2017-01-01');
        do {
            $months[$date->format('m/Y')] = $date->format('Y-m');
            $date->subMonth();
        } while ($date->gte($lastMonth));

        return $months;
    }

    /**
     *
     */
    private function loadTable1(): void
    {
        $table1 = (object)[];

        $startDate = Carbon::parse($this->selectedMonth)->startOfMonth();
        $endDate = Carbon::parse($this->selectedMonth)->endOfMonth();

        $table1->transactions = AccountTransaction::where('account_id', $this->account_id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $table1->monthlySummary = AccountMonthlySummary::where('account_id', $this->account_id)
            ->whereYear('month_date', $startDate->year)
            ->whereMonth('month_date', $startDate->month)
            ->first();

        $this->table1 = $table1;
    }

    /**
     *
     */
    private function loadTable2(): void
    {
        $table2 = (object)[];
        $account_id = $this->request->input('account_id', false);
        $selectedMonth = $this->request->input('month', false);
        if ($selectedMonth) $selectedMonth = Carbon::parse($selectedMonth);

        $table2->transactions = AccountTransaction::query()
            ->where('account_id', $account_id)
            ->whereNull("reconciliation_id")
            ->where('transaction_date', '<', $selectedMonth)
            ->get();

        $table2->amount = $table2->transactions->sum(function (AccountTransaction $transaction) {
            return $transaction->getCreditOrDebit();
        });

        $table2->variance = $this->table1->monthlySummary->beginning_balance ?? 0 + $table2->amount;

        $this->table2 = $table2;
    }

}