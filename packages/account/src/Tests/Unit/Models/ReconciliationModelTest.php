<?php

namespace Tests\Feature;

use Account\Models\Account;
use Account\Models\Transaction;
use Account\Services\ReconciliationService;
use Tests\TestCase;

class ReconciliationModelTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getTotalTransactionsAmount_method()
    {
        $account = factory(Account::class)->create();

        $transactions = collect([]);
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]));
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 1234, 'credit_amount' => 0]));
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 123, 'debit_amount' => 0]));
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 1234, 'debit_amount' => 0]));

        $reconciliation = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $this->assertEquals($reconciliation->getTotalTransactionsAmount(), 0);
    }
}
