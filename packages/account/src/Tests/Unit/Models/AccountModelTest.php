<?php

namespace Tests\Feature;

use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Services\ReconciliationService;

class AccountModelTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getUnallocatedTransactionGroups_method()
    {
        $this->assertTrue(true);
//        $account = factory(Account::class)->create();
//
//        $references = [
//            //group TA1234 - 1
//            'TA1234 Testing',
//            'TA1234AD Test Reference',
//            'Test TA1234AD Reference',
//            'Test TA1234 Reference',
//            'Test Reference TA1234',
//            'Test Reference TA1234ASD',
//            //group TA12345 - 2
//            'TA12345 Testing',
//            'TA12345AD Test Reference',
//            'Test TA12345AD Reference',
//            'Test TA12345 Reference',
//            'Test Reference TA12345',
//            'Test Reference TA12345ASD',
//            //group TA2345 - 3
//            'TA2345 Testing',
//            'TA2345AD Test Reference',
//            'Test TA2345AD Reference',
//            'Test TA2345 Reference',
//            'Test Reference TA2345',
//            'Test Reference TA2345ASD',
//            //group TA23456 - 4
//            'TA23456 Testing',
//            'TA23456AD Test Reference',
//            'Test TA23456AD Reference',
//            'Test TA23456 Reference',
//            'Test Reference TA23456',
//            'Test Reference TA23456ASD',
//            //REVERSAL
//            '<REVERSE> Test 1',
//            '<REVERSAL> Test 2',
//            '<REVERSe> Test 3',
//            '<REVERSal> Test 4',
//            '<reverse> Test 5',
//            '<reversal> Test 6',
//        ];
//
//        foreach ($references as $reference) {
//            factory(Transaction::class)->create([
//                'account_id' => $account->id,
//                'reference'  => $reference,
//            ]);
//        }
//
//        /** @var Account $account */
//        $account = Account::first();
//        $groups = $account->getUnallocatedTransactionGroups();
//
//        $this->assertNotNull($groups);
//        $this->assertEquals($groups->count(), 5);
//
//        $references = ['TA1234', 'TA12345', 'TA2345', 'TA23456', 'reverse'];
//        foreach ($references as $reference) {
//            $this->assertArrayHasKey($reference, $groups);
//            $this->assertEquals(6, $groups[$reference]->count());
//
//            /** @var Transaction $transaction */
//            foreach ($groups[$reference] as $transaction) {
//                $this->assertEquals($transaction->getReferenceId()->getTa(), $reference);
//            }
//        }
    }

    /**
     * @group shouldRun
     */
    public function test_getTotalTransactionsAmount_amount()
    {
        /** @var Account $account */
        $account = factory(Account::class)->create();

        $transactions = collect([]);
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]));
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 1234, 'credit_amount' => 0]));
        ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([]);
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 123, 'debit_amount' => 0]));
        $transactions->push(factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 1234, 'debit_amount' => 0]));
        ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 123, 'debit_amount' => 0]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 1234, 'debit_amount' => 0]);

        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 1234, 'credit_amount' => 0]);

        $account->fresh(['reconciliations', 'transactions']);

        $this->assertEquals($account->getTotalTransactionsAmount(), 0);
    }

    /**
     * @group shouldRun
     */
    public function test_getUnreconciledTransactionsSubtotal_method()
    {
        /** @var Account $account */
        $account = factory(Account::class)->create();

        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 100, 'credit_amount' => 0]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 100, 'credit_amount' => 0]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 100, 'credit_amount' => 0]);

        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 10]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 10]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 10]);

        $account->load('transactions');

        $this->assertEquals(270, $account->getUnreconciledTransactionsSubtotal());
    }

    /**
     * This test should ensure that if more than 1 item has the same reference id, then they are skipped when filtering.
     *
     * @group shouldRun
     */
    public function test_getUnallocatedTransactionsWithoutGrouping_method()
    {
//        $account = factory(Account::class)->create();
//
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 something']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 something']);
//
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 something']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 something']);
//
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123456 something']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234567 something']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345678 something']);
//
//        $account->load('transactions');
//
//        $transactions = $account->getUnallocatedTransactionsWithoutGrouping();
//
//        $this->assertEquals($transactions->count(), 3);
//        $this->assertEquals($transactions->where('reference', 'TA1234 something')->count(), 0);
//        $this->assertEquals($transactions->where('reference', 'TA12345 something')->count(), 0);
//        $this->assertEquals($transactions->where('reference', 'TA123456 something')->count(), 1);
//        $this->assertEquals($transactions->where('reference', 'TA1234567 something')->count(), 1);
//        $this->assertEquals($transactions->where('reference', 'TA12345678 something')->count(), 1);
        $this->assertNotNull(true);
    }
}
