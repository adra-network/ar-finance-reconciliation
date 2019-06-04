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
    public function test_get_unallocated_transaction_groups_method()
    {
        $account = factory(Account::class)->create();

        $references = [
            //group TA1234 - 1
            'TA1234 Testing',
            'TA1234AD Test Reference',
            'Test TA1234AD Reference',
            'Test TA1234 Reference',
            'Test Reference TA1234',
            'Test Reference TA1234ASD',
            //group TA12345 - 2
            'TA12345 Testing',
            'TA12345AD Test Reference',
            'Test TA12345AD Reference',
            'Test TA12345 Reference',
            'Test Reference TA12345',
            'Test Reference TA12345ASD',
            //group TA2345 - 3
            'TA2345 Testing',
            'TA2345AD Test Reference',
            'Test TA2345AD Reference',
            'Test TA2345 Reference',
            'Test Reference TA2345',
            'Test Reference TA2345ASD',
            //group TA23456 - 4
            'TA23456 Testing',
            'TA23456AD Test Reference',
            'Test TA23456AD Reference',
            'Test TA23456 Reference',
            'Test Reference TA23456',
            'Test Reference TA23456ASD',
            //REVERSAL
            '<REVERSE> Test 1',
            '<REVERSAL> Test 2',
            '<REVERSe> Test 3',
            '<REVERSal> Test 4',
            '<reverse> Test 5',
            '<reversal> Test 6',
        ];

        foreach ($references as $reference) {
            factory(AccountTransaction::class)->create([
                'account_id' => $account->id,
                'reference'  => $reference,
            ]);
        }

        /** @var Account $account */
        $account = Account::first();
        $groups = $account->getUnallocatedTransactionGroups();

        $this->assertNotNull($groups);
        $this->assertEquals($groups->count(), 5);

        $references = ['TA1234', 'TA12345', 'TA2345', 'TA23456', 'reverse'];
        foreach ($references as $reference) {
            $this->assertArrayHasKey($reference, $groups);
            $this->assertEquals(6, $groups[$reference]->count());

            /** @var AccountTransaction $transaction */
            foreach ($groups[$reference] as $transaction) {
                $this->assertEquals($transaction->getReferenceId(), $reference);
            }
        }
    }

    /**
     * @group shouldRun
     */
    public function test_get_sum_of_all_transactions_amount()
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

        $this->assertEquals($account->getTotalTransactionsAmount(), 0);
    }

    /**
     * @group shouldRun
     */
    public function test_get_variance_method()
    {
        /** @var Account $account */
        $account = factory(Account::class)->create();

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 100, 'credit_amount' => 0]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 100, 'credit_amount' => 0]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 100, 'credit_amount' => 0]);

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 10]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 10]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 10]);

        $account->load('transactions');

        $this->assertEquals(270, $account->getVariance());
    }
}
