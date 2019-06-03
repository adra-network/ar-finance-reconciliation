<?php

use App\Account;
use App\AccountMonthlySummary;
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
        $batches = [
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
            ],
            //Reconciliation id - 2
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 100],
                    (object)['credit' => 100, 'debit' => 0],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => -100,
            ],
            //Reconciliation id - 3
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 100],
                    (object)['credit' => 50, 'debit' => 0],
                    (object)['credit' => 50, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
            //Reconciliation id - 4
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
            //Reconciliation id - 5
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
            //Reconciliation id - 6
            (object)[
                'transactions' => [
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 0, 'debit' => 50],
                    (object)['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
        ];
        $account = factory(Account::class)->create();
        factory(AccountMonthlySummary::class)->create([
            'account_id' => $account->id,
        ]);
        foreach ($batches as $batch) {

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

        }
        //this will change reconciliation dates, to make them each a month older
        //will use to test if show previous works okay
        $c = 0;
        foreach (Reconciliation::all() as $reconciliation) {
            if ($reconciliation->isFullyReconciled()) {
                $reconciliation->created_at = now()->startOfMonth()->subMonths($c);
                $reconciliation->save();
                $c++;
            }
        }

        $references = [
            //group TA1234 - 1
            'TA1234 Testing',
            'TA1234AD Test Reference',
            'Test TA1234AD Reference',
            'Test TA1234 Reference',
            'Test Reference TA1234',
            //group TA12345 - 2
            'TA12345 Testing',
            'TA12345AD Test Reference',
            'Test TA12345AD Reference',
            'Test TA12345 Reference',
            'Test Reference TA12345',
            //group TA2345 - 3
            'TA2345 Testing',
            'TA2345AD Test Reference',
            'Test TA2345AD Reference',
            'Test TA2345 Reference',
            'Test Reference TA2345',
            //group TA23456 - 4
            'TA23456 Testing',
            'TA23456AD Test Reference',
            'Test TA23456AD Reference',
            'Test TA23456 Reference',
            'Test Reference TA23456',
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
                'reference' => $reference,
            ]);
        }

        factory(AccountTransaction::class, 10)->create([
            'account_id' => $account->id,
        ]);

        //seed for admin.accounts.transactions table2
        for ($i = 1; $i < 5; $i++) {
            factory(AccountTransaction::class, 3)->create([
                'account_id' => $account->id,
                'transaction_date' => now()->subMonths($i),
            ]);
        }

    }

}
