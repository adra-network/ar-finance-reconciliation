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
}
