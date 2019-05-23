<?php

use App\Account;
use App\AccountTransaction;
use App\Reconciliation;
use App\Services\ReconciliationService;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bathes = [
            //Reconciliation id - 1
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 100],
                    (object)['credit' => 10, 'debit' => 0],
                    (object)['credit' => 10, 'debit' => 0],
                    (object)['credit' => 10, 'debit' => 0],
                    (object)['credit' => 20, 'debit' => 0],
                ],
                'shouldReconcileTo' => 50,
                'created_at' => now(),
            ],
            //Reconciliation id - 2
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 100],
                    (object)['credit' => 100, 'debit' => 0],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => -100,
                'created_at' => now(),
            ],
            //Reconciliation id - 3
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 100],
                    (object)['credit' => 50, 'debit' => 0],
                    (object)['credit' => 50, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
                'created_at' => now()->subYear(),
            ],
            //Reconciliation id - 4
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
                'created_at' => now()->subMonth(),
            ],
            //Reconciliation id - 5
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
                'created_at' => now()->subMonths(2),
            ],
            //Reconciliation id - 6
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
                'created_at' => now()->subMonths(3),
            ],
        ];
        $account = factory(Account::class)->create();
        foreach ($bathes as $batch) {

            $transactionsToReconcile = collect([]);

            //Create some transactions
            foreach ($batch->transactions as $transaction) {
                $transaction = factory(AccountTransaction::class)->create([
                    'account_id' => $account->id,
                    'debit_amount' => $transaction->debit,
                    'credit_amount' => $transaction->credit,
                ]);
                $transactionsToReconcile->push($transaction);
            }
            ReconciliationService::reconcileTransactions($transactionsToReconcile->pluck('id')->toArray());

            //this will change reconciliation dates, to make them each a month older
            //will use to test if show previous works okay
            $c = 0;
            foreach(Reconciliation::all() as $reconciliation) {
                if ($reconciliation->isFullyReconciled()) {
                    $reconciliation->created_at = now()->subMonths($c);
                    $reconciliation->save();
                    $c++;
                }
            }
        }

        factory(AccountTransaction::class, 5)->create([
            'account_id' => $account->id,
        ]);
    }

}
