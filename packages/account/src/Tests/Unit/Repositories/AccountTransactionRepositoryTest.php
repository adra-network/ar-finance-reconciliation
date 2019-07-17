<?php

namespace Tests\Feature;

use Account\Models\Account;
use Account\Models\Transaction;
use Account\Repositories\TransactionRepository;
use Account\Services\ReconciliationService;
use Tests\TestCase;

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
        $r0 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([
            //3
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            //4
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        //unreconciled
        $r1 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        //5
        factory(Transaction::class)->create(['account_id' => $account->id]);
        //6
        factory(Transaction::class)->create(['account_id' => $account->id]);
        //7
        factory(Transaction::class)->create(['account_id' => $account->id]);

        $transactions = TransactionRepository::getUnreconciledTransactions();

        $this->assertEquals($transactions->count(), 5);

        $this->assertEquals($transactions->where('id', 1)->count(), 0);
        $this->assertEquals($transactions->where('id', 2)->count(), 0);

        $this->assertEquals($transactions->where('id', 3)->count(), 1);
        $this->assertEquals($transactions->where('id', 4)->count(), 1);
        $this->assertEquals($transactions->where('id', 5)->count(), 1);
        $this->assertEquals($transactions->where('id', 6)->count(), 1);
        $this->assertEquals($transactions->where('id', 7)->count(), 1);
    }

    /**
     * @group shouldRun
     */
    public function test_getUnallocatedTransactionsWhereReferenceIdIs_function()
    {
        $account = factory(Account::class)->create();

        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123 test']);
        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123 test']);

        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 test']);
        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 test']);

        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 test']);
        factory(Transaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 test']);

        $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA123');
        $this->assertEquals($transactions->count(), 2);
        $this->assertEquals($transactions->where('id', 1)->count(), 1);
        $this->assertEquals($transactions->where('id', 2)->count(), 1);

        $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA1234');
        $this->assertEquals($transactions->count(), 2);
        $this->assertEquals($transactions->where('id', 3)->count(), 1);
        $this->assertEquals($transactions->where('id', 4)->count(), 1);

        $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA12345');
        $this->assertEquals($transactions->count(), 2);
        $this->assertEquals($transactions->where('id', 5)->count(), 1);
        $this->assertEquals($transactions->where('id', 6)->count(), 1);
    }
}
