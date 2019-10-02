<?php

namespace Account\Services;

use Account\Models\Account;
use Account\Models\Transaction;
use Account\Models\AccountImport;
use Account\Models\MonthlySummary;

class AccountPageTableService
{
    /** @var AccountImport */
    private $accountImport;

    /** @var Account */
    private $account;

    /** @var object */
    private $table1;

    /** @var object */
    private $table2;

    /**
     * AccountPageTableService constructor.
     * @param Account $account
     * @param AccountImport $accountImport
     */
    public function __construct(Account $account, AccountImport $accountImport)
    {
        $this->account = $account;
        $this->accountImport = $accountImport;
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

        $table1->transactions = Transaction::where('account_id', $this->account->id)->where('account_import_id', $this->accountImport->id)->get();
        $table1->monthlySummary = MonthlySummary::where('account_id', $this->account->id)->where('account_import_id', $this->accountImport->id)->first();

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

        $table2->transactions = Transaction::query()
            ->where('account_id', $this->account->id)
            ->whereNull('reconciliation_id')
            ->whereDate('transaction_date', '<', $this->accountImport->date_from)
            ->get();

        $table2->amount = $table2->transactions->sum(function (Transaction $transaction) {
            return $transaction->getCreditOrDebit();
        });

        $table2->variance = $this->getTable1()->monthlySummary->beginning_balance ?? 0 + $table2->amount;

        $this->table2 = $table2;

        return $table2;
    }
}
