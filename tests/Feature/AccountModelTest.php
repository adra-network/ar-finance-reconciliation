<?php

namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use App\Services\ReconciliationService;
use Tests\TestCase;

class AccountModelTest extends TestCase
{

    /**
     * @group shouldRun
     */
    public function test_get_transactions_total_method()
    {
        /** @var Account $account */
        $account = factory(Account::class)->create();

        $transactions = collect([]);
        $transactions->push(factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]));
        $transactions->push(factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 1234, 'credit_amount' => 0]));
        ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([]);
        $transactions->push(factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 123, 'debit_amount' => 0]));
        $transactions->push(factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 1234, 'debit_amount' => 0]));
        ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 123, 'debit_amount' => 0]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 1234, 'debit_amount' => 0]);

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 1234, 'credit_amount' => 0]);

        $account->fresh(['reconciliations', 'transactions']);

        $this->assertEquals($account->getTransactionsTotal(), 0);
    }

    /**
     * @group shouldRun
     */
    public function test_get_reference_id_method()
    {
        $account = factory(Account::class)->create();

        $references = [
            'TA1234 Testing',
            'TA1234AD Test Reference',
            'Test TA1234AD Reference',
            'Test TA1234 Reference',
            'Test Reference TA1234',
        ];

        foreach ($references as $reference) {
            /** @var AccountTransaction $transaction */
            $transaction = factory(AccountTransaction::class)->create([
                'account_id' => $account->id,
                'reference' => $reference,
            ]);

            $this->assertEquals($transaction->getReferenceId(), 'TA1234');
        }

        $transaction = factory(AccountTransaction::class)->create([
            'account_id' => $account->id,
            'reference' => 'TAasd',
        ]);
        $this->assertNull($transaction->getReferenceId(), 'TA1234');

    }

}