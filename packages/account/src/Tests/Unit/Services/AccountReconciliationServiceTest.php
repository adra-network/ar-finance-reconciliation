<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Models\Reconciliation;
use Account\Services\ReconciliationService;

class AccountReconciliationServiceTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getBatchTableReconciliations_method()
    {
        /** @var Account $account */
        $account = factory(Account::class)->create();

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 100, 'debit_amount' => 0]),
        ]);
        $r0 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        $r1 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        $r2 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());
        $r2->created_at = now()->subMonths(1);
        $r2->save();

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        $r3 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());
        $r3->created_at = now()->subMonths(2);
        $r3->save();

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        $r4 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());
        $r4->created_at = now()->subMonths(3);
        $r4->save();

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 100]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 110, 'debit_amount' => 0]),
        ]);
        $r5 = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());
        $r5->created_at = now()->subMonths(3);
        $r5->save();

        $account = Account::with('reconciliations')->first();

        $reconciliations = $account->getBatchTableReconciliations();
        $this->assertEquals($reconciliations->count(), 5);
        $this->assertEquals($account->reconciliations->offsetGet(1)->id, $r1->id);
        $this->assertEquals($account->reconciliations->offsetGet(2)->id, $r2->id);
        $this->assertEquals($account->reconciliations->offsetGet(3)->id, $r3->id);
        $this->assertEquals($account->reconciliations->offsetGet(4)->id, $r4->id);
        $this->assertEquals($account->reconciliations->offsetGet(5)->id, $r5->id);

        $account->setBatchTableWithPreviousMonths(1);
        $reconciliations = $account->getBatchTableReconciliations();
        $this->assertEquals($reconciliations->count(), 3);
        $this->assertEquals($account->reconciliations->offsetGet(0)->id, $r0->id);
        $this->assertEquals($account->reconciliations->offsetGet(1)->id, $r1->id);
        $this->assertEquals($account->reconciliations->offsetGet(2)->id, $r2->id);

        $account->setBatchTableWithPreviousMonths(2);
        $reconciliations = $account->getBatchTableReconciliations();
        $this->assertEquals($reconciliations->count(), 4);
        $this->assertEquals($account->reconciliations->offsetGet(0)->id, $r0->id);
        $this->assertEquals($account->reconciliations->offsetGet(1)->id, $r1->id);
        $this->assertEquals($account->reconciliations->offsetGet(2)->id, $r2->id);
        $this->assertEquals($account->reconciliations->offsetGet(3)->id, $r3->id);

        $account->setBatchTableWithPreviousMonths(3);
        $reconciliations = $account->getBatchTableReconciliations();
        $this->assertEquals($reconciliations->count(), 6);
        $this->assertEquals($account->reconciliations->offsetGet(0)->id, $r0->id);
        $this->assertEquals($account->reconciliations->offsetGet(1)->id, $r1->id);
        $this->assertEquals($account->reconciliations->offsetGet(2)->id, $r2->id);
        $this->assertEquals($account->reconciliations->offsetGet(3)->id, $r3->id);
        $this->assertEquals($account->reconciliations->offsetGet(4)->id, $r4->id);
        $this->assertEquals($account->reconciliations->offsetGet(5)->id, $r5->id);
    }

    /**
     * @group shouldRun
     */
    public function test_if_account_transactions_go_missing_after_reconciliation()
    {
        $user = User::find(1);
        $account = factory(Account::class)->create();
        foreach (self::getBatchesForTesting() as $batch) {
            $transactionsToReconcile = collect([]);

            //Create some transactions
            foreach ($batch->transactions as $transaction) {
                $transaction = factory(Transaction::class)->create([
                    'account_id' => $account->id,
                    'debit_amount' => $transaction->debit,
                    'credit_amount' => $transaction->credit,
                ]);
                $transactionsToReconcile->push($transaction);
            }

            //Go to the page
            $response = $this->actingAs($user)->get(route('account.transactions.index'));
            //And see that they are indeed there
            foreach ($transactionsToReconcile as $transaction) {
                $response->assertSee($transaction->code);
            }

            //Then reconcile them
            ReconciliationService::reconcileTransactions($transactionsToReconcile->pluck('id')->toArray());

            //Go to the same page again
            $response = $this->actingAs($user)->get(route('account.transactions.index'));

            //And ensure that they are missing if batch should reconcile or vise versa
            foreach ($transactionsToReconcile as $transaction) {
                if ($batch->shouldReconcileTo !== 0) {
                    $response->assertSee($transaction->code);
                } else {
                    $response->assertDontSee($transaction->code);
                }
            }
        }
    }

    public function test_reconciling_transactions_with_cents_to_zero()
    {

        /** @var Account $account */
        $account = factory(Account::class)->create();

        $transactions = collect([
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 0, 'debit_amount' => 512.33]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 150, 'debit_amount' => 0]),
            factory(Transaction::class)->create(['account_id' => $account->id, 'credit_amount' => 362.33, 'debit_amount' => 0]),
        ]);

        $reconciliation = ReconciliationService::reconcileTransactions($transactions->pluck('id')->toArray());
        $this->assertTrue($reconciliation->isFullyReconciled());
        $this->assertEquals(0.0, $reconciliation->getTotalTransactionsAmount());
    }

    /**
     * @group shouldRun
     */
    public function test_account_reconciliation_service()
    {
        $account = factory(Account::class)->create();
        foreach (self::getBatchesForTesting() as $batch) {
            $transactionsToReconcile = [];

            foreach ($batch->transactions as $transaction) {
                $transaction = factory(Transaction::class)->create([
                    'account_id' => $account->id,
                    'debit_amount' => $transaction->debit,
                    'credit_amount' => $transaction->credit,
                ]);

                $transactionsToReconcile[] = $transaction->id;
            }

            $reconciliation = ReconciliationService::reconcileTransactions($transactionsToReconcile);

            //ASSERT THAT TRANSACTIONS RECONCILED CORRECTLY
            if ($batch->shouldReconcileTo !== 0) {
                $this->assertFalse($reconciliation->isFullyReconciled());
                $this->assertFalse($reconciliation->is_fully_reconciled);
            } else {
                $this->assertTrue($reconciliation->isFullyReconciled());
                $this->assertTrue($reconciliation->is_fully_reconciled);
            }
        }

        //Add new transactions to fully reconcile transactions of first reconciliation
        $reconciliation1 = Reconciliation::with('transactions')->find(1);
        $transaction = factory(Transaction::class)->create([
            'account_id' => $account->id,
            'debit_amount' => 0,
            'credit_amount' => 50,
        ]);
        $transactions = $reconciliation1->transactions->pluck('id')->toArray();
        $transactions[] = $transaction->id;
        $reconciliation1 = ReconciliationService::reconcileTransactions($transactions);
        $this->assertEquals($reconciliation1->transactions->pluck('id')->toArray(), $transactions);
        $this->assertTrue($reconciliation1->isFullyReconciled());
        $this->assertTrue($reconciliation1->is_fully_reconciled);

        //Add a bebit transaction and check if transactions reconcile
        $reconciliation2 = Reconciliation::with('transactions')->find(2);
        $transaction = factory(Transaction::class)->create([
            'account_id' => $account->id,
            'debit_amount' => 100,
            'credit_amount' => 0,
        ]);
        $transactions = $reconciliation2->transactions->pluck('id')->toArray();
        $transactions[] = $transaction->id;
        $reconciliation2 = ReconciliationService::reconcileTransactions($transactions);
        $this->assertEquals($reconciliation2->transactions->pluck('id')->toArray(), $transactions);
        $this->assertTrue($reconciliation2->isFullyReconciled());
        $this->assertTrue($reconciliation2->is_fully_reconciled);

        //Remove credit transaction and check if transactions unreconcile
        $reconciliation3 = Reconciliation::with('transactions')->find(3);
        //SortByDesc and take(2) ensures that we take one debit and one credit transactions because there are 3 total and 2 of those are credits
        $transactions = $reconciliation3->transactions->sortByDesc('debit')->take(2)->pluck('id')->toArray();
        $reconciliation3 = ReconciliationService::reconcileTransactions($transactions);
        $this->assertEquals($reconciliation3->transactions->pluck('id')->toArray(), $transactions);
        $this->assertFalse($reconciliation3->isFullyReconciled());
        $this->assertFalse($reconciliation3->is_fully_reconciled);

        //Remove debit transaction and check if transactions unreconcile
        $reconciliation4 = Reconciliation::with('transactions')->find(3);
        //SortByDesc and take(2) ensures that we take one debit and one credit transactions because there are 3 total and 2 of those are credits
        $transactions = $reconciliation4->transactions->sortByDesc('credit')->take(2)->pluck('id')->toArray();
        $reconciliation4 = ReconciliationService::reconcileTransactions($transactions);
        $this->assertEquals($reconciliation4->transactions->pluck('id')->toArray(), $transactions);
        $this->assertFalse($reconciliation4->isFullyReconciled());
        $this->assertFalse($reconciliation4->is_fully_reconciled);

        //Add a transaction with different reconciliation and expect an error
        $transactions = array_merge(
            $reconciliation1->transactions->pluck('id')->toArray(),
            $reconciliation2->transactions->pluck('id')->toArray()
        );

        try {
            ReconciliationService::reconcileTransactions($transactions);
            $this->fail('Didin\'t receive expected exception when trying to reconcile transactions with different reconciliations');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'Can\'t reconcile because given transactions have diferent reconciliations');
        }

        //Reconcile first transaction and check if its reconciliation is deleted and transactions dont have reconciliation id
        //Rule is that if we try to reconcile a single transaction then it deletes the reconciliation to protect from unwanted results in frontend and backend
        ReconciliationService::reconcileTransactions([1]);
        $this->assertDatabaseMissing('reconciliations', ['id' => 1]);
        $this->assertDatabaseHas('account_transactions', ['id' => 1, 'reconciliation_id' => null]);
        $this->assertDatabaseHas('account_transactions', ['id' => 2, 'reconciliation_id' => null]);
        $this->assertDatabaseHas('account_transactions', ['id' => 3, 'reconciliation_id' => null]);
        $this->assertDatabaseHas('account_transactions', ['id' => 4, 'reconciliation_id' => null]);
        $this->assertDatabaseHas('account_transactions', ['id' => 5, 'reconciliation_id' => null]);
        //Transaction 6 does not belong to reconciliation 1 so it should have its reconciliation id
        $this->assertDatabaseHas('account_transactions', ['id' => 6, 'reconciliation_id' => 2]);
    }

    //debit amount needs to be covered by a credit
    //credit is displayed with a minus `-` in front
    private static function getBatchesForTesting(): array
    {
        return [
            //Reconciliation id - 1
            (object) [
                'transactions' => [
                    (object) ['credit' => 0, 'debit' => 100],
                    (object) ['credit' => 10, 'debit' => 0],
                    (object) ['credit' => 10, 'debit' => 0],
                    (object) ['credit' => 10, 'debit' => 0],
                    (object) ['credit' => 20, 'debit' => 0],
                ],
                'shouldReconcileTo' => 50,
            ],
            //Reconciliation id - 2
            (object) [
                'transactions' => [
                    (object) ['credit' => 0, 'debit' => 100],
                    (object) ['credit' => 100, 'debit' => 0],
                    (object) ['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => -100,
            ],
            //Reconciliation id - 3
            (object) [
                'transactions' => [
                    (object) ['credit' => 0, 'debit' => 100],
                    (object) ['credit' => 50, 'debit' => 0],
                    (object) ['credit' => 50, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
            //Reconciliation id - 4
            (object) [
                'transactions' => [
                    (object) ['credit' => 0, 'debit' => 50],
                    (object) ['credit' => 0, 'debit' => 50],
                    (object) ['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
        ];
    }
}
