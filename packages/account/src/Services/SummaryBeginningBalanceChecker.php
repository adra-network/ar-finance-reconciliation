<?php

namespace Account\Services;

use Account\Models\MonthlySummary;
use Account\Models\Transaction;

class SummaryBeginningBalanceChecker
{
    public $currentBalance;
    public $beginningBalance;
    public $balanceInSync = true;

    public function __construct(MonthlySummary $summary)
    {
        $this->currentBalance = Transaction::where('account_id', $summary->account_id)
            ->where('account_import_id', '<', $summary->account_import_id)
            ->get()
            ->sum(function (Transaction $transaction) {
                //convert to cents
                return round($transaction->getCreditOrDebit() * 100);
            });

        $this->beginningBalance = round($summary->beginning_balance * 100);
        $this->balanceInSync    = (bool)($this->beginningBalance !== $this->currentBalance);
    }

    public function diff()
    {
        return $this->beginningBalance - $this->currentBalance;
    }

    public function inSync()
    {
        return $this->balanceInSync;
    }
}
