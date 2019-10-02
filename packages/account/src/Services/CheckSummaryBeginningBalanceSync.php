<?php

namespace Account\Services;

use Account\Models\Transaction;
use Account\Models\MonthlySummary;

class CheckSummaryBeginningBalanceSync
{
    public function __invoke(MonthlySummary $summary)
    {
        $currentBalance = Transaction::where('account_id', $summary->account_id)->get()->sum(function (Transaction $transaction) {
            //convert to cents
            return (int) ($transaction->getCreditOrDebit() * 100);
        });

        $beginningBalance = (int) ($summary->beginning_balance * 100);
        if ($beginningBalance !== $currentBalance) {
            $summary->beginningBalanceNotInSync();
        }
    }
}
