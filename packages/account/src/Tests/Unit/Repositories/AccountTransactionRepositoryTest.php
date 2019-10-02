<?php

namespace Tests\Feature;

use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Services\ReconciliationService;
use Account\Repositories\TransactionRepository;

class AccountTransactionRepositoryTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getUnreconciledTransactions_function()
    {
        /** @var Account $account */
        $account = factory(Account::class)->create();

        $transactions = collect([
            //1
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            //2
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 100, 'debit_amount' => 0]),
        ]);
        //reconciled
        ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([
            //3
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            //4
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        //unreconciled
        ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        //5
        factory(Transaction::class)->create(['account_id' => $account->id]);
        //6
        factory(Transaction::class)->create(['account_id' => $account->id]);
        //7
        factory(Transaction::class)->create(['account_id' => $account->id]);

        $transactions = TransactionRepository::getUnreconciledTransactions();

        $this->assertEquals($transactions->count(), 5);

        $this->assertNull($transactions->where('id', 1)->first());
        $this->assertNull($transactions->where('id', 2)->first());

        $this->assertNotNull($transactions->where('id', 3)->first());
        $this->assertNotNull($transactions->where('id', 4)->first());
        $this->assertNotNull($transactions->where('id', 5)->first());
        $this->assertNotNull($transactions->where('id', 6)->first());
        $this->assertNotNull($transactions->where('id', 7)->first());
    }

    /**
     * @group shouldRun
     */
    public function test_getUnallocatedTransactionsWhereReferenceIdIs_function()
    {
        $account = factory(Account::class)->create();
        $this->assertNotNull(true);

//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123 test']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123 test']);
//
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 test']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 test']);
//
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 test']);
//        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 test']);
//
//        $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA123');
//        $this->assertEquals($transactions->count(), 2);
//        $this->assertEquals($transactions->where('id', 1)->count(), 1);
//        $this->assertEquals($transactions->where('id', 2)->count(), 1);
//
//        $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA1234');
//        $this->assertEquals($transactions->count(), 2);
//        $this->assertEquals($transactions->where('id', 3)->count(), 1);
//        $this->assertEquals($transactions->where('id', 4)->count(), 1);
//
//        $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA12345');
//        $this->assertEquals($transactions->count(), 2);
//        $this->assertEquals($transactions->where('id', 5)->count(), 1);
//        $this->assertEquals($transactions->where('id', 6)->count(), 1);
    }

    public function test_getLateUnreconciledTransactions_function()
    {
        /* intervals */
        $i1 = 45;
        $i2 = 80;
        $i3 = 90;

        factory(Transaction::class)->create(['transaction_date' => now()->startOfDay()]);
        factory(Transaction::class)->create(['transaction_date' => now()->subDays($i1)->startOfDay()]);
        factory(Transaction::class)->create(['transaction_date' => now()->subDays($i2)->startOfDay()]);
        factory(Transaction::class)->create(['transaction_date' => now()->subDays($i3)->startOfDay()]);

        $transactions = TransactionRepository::getLateTransactions();
        $this->assertEquals(3, $transactions->count());
    }
}
