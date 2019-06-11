<?php

namespace Tests\Feature;

use App\Account;
use App\AccountMonthlySummary;
use App\AccountTransaction;
use App\Services\AccountPageTableService;
use App\Services\ReconciliationService;
use Tests\TestCase;

class AccountPageTableServiceTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getTable1_method()
    {
        $account = factory(Account::class)->create();

        //in bounds
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->startOfMonth()->format('Y-m-d')]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->endOfMonth()->format('Y-m-d')]);

        //out of bounds
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->startOfMonth()->subSecond()->format('Y-m-d')]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->endOfMonth()->addSecond()->format('Y-m-d')]);

        factory(AccountMonthlySummary::class)->create(['account_id' => $account->id]);

        $s = new AccountPageTableService($account, now());
        $table1 = $s->getTable1();

        $this->assertTrue(isset($table1->transactions));
        $this->assertTrue(isset($table1->monthlySummary));

        $this->assertEquals($table1->transactions->count(), 2);
        $this->assertEquals($table1->transactions->where('id', 1)->count(), 1);
        $this->assertEquals($table1->transactions->where('id', 2)->count(), 1);

        $this->assertEquals($table1->transactions->where('id', 3)->count(), 0);
        $this->assertEquals($table1->transactions->where('id', 4)->count(), 0);
    }

    /**
     * @group shouldRun
     */
    public function test_getTable2_method()
    {
        $account = factory(Account::class)->create();

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subDay()->format('Y-m-d'), 'credit_amount' => 0, 'debit_amount' => 110]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subDay()->format('Y-m-d'), 'credit_amount' => 100, 'debit_amount' => 0]);

        factory(AccountTransaction::class)->create(['account_id' => $account->id]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id]);
        ReconciliationService::reconcileTransactions([3, 4]);

        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->addDay()->format('Y-m-d')]);
        factory(AccountTransaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->addDay()->format('Y-m-d')]);

        factory(AccountMonthlySummary::class)->create(['account_id' => $account->id, 'beginning_balance' => 120]);

        $s = new AccountPageTableService($account, now());
        $table2 = $s->getTable2();

        $this->assertTrue(isset($table2->transactions));
        $this->assertTrue(isset($table2->amount));
        $this->assertTrue(isset($table2->variance));

        $this->assertEquals($table2->transactions->count(), 2);
        $this->assertEquals($table2->transactions->where('id', 1)->count(), 1);
        $this->assertEquals($table2->transactions->where('id', 2)->count(), 1);

        $this->assertEquals($table2->transactions->where('id', 3)->count(), 0);
        $this->assertEquals($table2->transactions->where('id', 4)->count(), 0);
        $this->assertEquals($table2->transactions->where('id', 5)->count(), 0);
        $this->assertEquals($table2->transactions->where('id', 6)->count(), 0);

        $this->assertEquals($table2->amount, 10);
        $this->assertEquals($table2->variance, '120');
    }
}
