<?php

namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use App\Repositories\AccountTransactionRepository;
use App\Services\ReconciliationService;
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
            factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            //2
            factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 100, 'debit_amount' => 0]),
        ]);
        //reconciled
        $r0 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([
            //3
            factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            //4
            factory(AccountTransaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        //unreconciled
        $r1 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        //5
        factory(AccountTransaction::class)->create(['account_id' => $account->id]);
        //6
        factory(AccountTransaction::class)->create(['account_id' => $account->id]);
        //7
        factory(AccountTransaction::class)->create(['account_id' => $account->id]);

        $transactions = AccountTransactionRepository::getUnreconciledTransactions();

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

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123 test']);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'reference' => 'TA123 test']);

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 test']);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'reference' => 'TA1234 test']);

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 test']);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'reference' => 'TA12345 test']);

        $transactions = AccountTransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA123');
        $this->assertEquals($transactions->count(), 2);
        $this->assertEquals($transactions->where('id', 1)->count(), 1);
        $this->assertEquals($transactions->where('id', 2)->count(), 1);

        $transactions = AccountTransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA1234');
        $this->assertEquals($transactions->count(), 2);
        $this->assertEquals($transactions->where('id', 3)->count(), 1);
        $this->assertEquals($transactions->where('id', 4)->count(), 1);

        $transactions = AccountTransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs('TA12345');
        $this->assertEquals($transactions->count(), 2);
        $this->assertEquals($transactions->where('id', 5)->count(), 1);
        $this->assertEquals($transactions->where('id', 6)->count(), 1);
    }
}
