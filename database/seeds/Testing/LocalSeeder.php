<?php

use App\User;
use Account\Models\Account;
use Account\Models\Transaction;
use Illuminate\Database\Seeder;
use Account\Models\MonthlySummary;
use Account\Models\Reconciliation;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;
use Account\Services\ReconciliationService;

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
            //Reconciliation id - 5
            (object) [
                'transactions' => [
                    (object) ['credit' => 0, 'debit' => 50],
                    (object) ['credit' => 0, 'debit' => 50],
                    (object) ['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
            //Reconciliation id - 6
            (object) [
                'transactions' => [
                    (object) ['credit' => 0, 'debit' => 50],
                    (object) ['credit' => 0, 'debit' => 50],
                    (object) ['credit' => 100, 'debit' => 0],
                ],
                'shouldReconcileTo' => 0,
            ],
        ];
        $account = factory(Account::class)->create();
        factory(MonthlySummary::class)->create([
            'account_id' => $account->id,
        ]);
        foreach ($batches as $batch) {
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

            "December'18CC ADRA Travel: Hales CC Statement Exp",
            "Hilda Madanat December '17 CC Canada Travel Expense",
            "Nick DeFranco Dec'18 CC, Jan & October '19 Trips to Citiban Laurel",
        ];

        foreach ($references as $reference) {
            factory(Transaction::class)->create([
                'account_id' => $account->id,
                'reference' => $reference,
            ]);
        }

        factory(Transaction::class, 10)->create([
            'account_id' => $account->id,
        ]);

        //seed for admin.accounts.transactions table2
        for ($i = 1; $i < 5; $i++) {
            factory(Transaction::class, 3)->create([
                'account_id' => $account->id,
                'transaction_date' => now()->subMonths($i),
            ]);
        }

        factory(Account::class, 10)->create();

        factory(CallerPhoneNumber::class, 20)->create()->each(function ($number) {
            factory(PhoneTransaction::class, 5)->create(['caller_phone_number_id' => $number->id]);
        });

        factory(AccountPhoneNumber::class, 20)->create();

        //seeds data for late transactions menu item
        $users = factory(User::class, 5)->create(['email_notifications_enabled' => false, 'logged_in_at' => now()->subDays(90)]);
        foreach ($users as $user) {
            $acc = factory(Account::class)->create(['user_id' => $user->id]);
            factory(Transaction::class, 10)->create(['account_id' => $acc->id, 'transaction_date' => now()->subDays(55)]);
        }
    }
}
