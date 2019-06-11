<?php

namespace App\Services;

use App\Account;
use App\AccountMonthlySummary;
use App\AccountTransaction;
use Carbon\CarbonInterface;

class AccountPageTableService
{
    /** @var CarbonInterface */
    private $month;

    /** @var Account */
    private $account;

    /** @var object */
    private $table1;

    /** @var object */
    private $table2;

    /**
     * AccountPageTableService constructor.
     *
     * @param Account         $account
     * @param CarbonInterface $month
     */
    public function __construct(Account $account, CarbonInterface $month)
    {
        $this->account = $account;
        $this->month = $month;
    }

    /**
     * @return object
     */
    public function getTable1(): object
    {
        if (isset($this->table1)) {
            return $this->table1;
        }

        $table1 = (object) [];

        //looks like laravel is in 'inclusive' on the start date for some reason.
        $startDate = $this->month->copy()->startOfMonth();
        $endDate = $this->month->copy()->endOfMonth();

        $table1->transactions = AccountTransaction::where('account_id', $this->account->id)
            ->whereBetween('transaction_date', [$startDate->copy()->subSecond(), $endDate])
            ->get();

        $table1->monthlySummary = AccountMonthlySummary::where('account_id', $this->account->id)
            ->whereYear('month_date', (string) $startDate->year)
            ->whereMonth('month_date', (string) $startDate->month)
            ->first();

        $this->table1 = $table1;

        return $table1;
    }

    /**
     * @return object
     */
    public function getTable2(): object
    {
        if (isset($this->table2)) {
            return $this->table2;
        }

        $table2 = (object) [];

        $table2->transactions = AccountTransaction::query()
            ->where('account_id', $this->account->id)
            ->whereNull('reconciliation_id')
            ->where('transaction_date', '<', $this->month)
            ->get();

        $table2->amount = $table2->transactions->sum(function (AccountTransaction $transaction) {
            return $transaction->getCreditOrDebit();
        });

        $table2->variance = $this->getTable1()->monthlySummary->beginning_balance ?? 0 + $table2->amount;

        $this->table2 = $table2;

        return $table2;
    }
}
