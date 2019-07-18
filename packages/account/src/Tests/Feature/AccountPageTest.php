<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Models\MonthlySummary;

class AccountPageTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_accounts_and_months_dropdowns_and_no_table()
    {
        $accounts = factory(Account::class, 10)->create();

        $user = User::find(1);
        $response = $this->actingAs($user)
            ->get(route('account.transactions.summary'));

        $response->assertViewHas('table1', null);
        $response->assertViewHas('table2', null);
        $response->assertViewHas('batchTable', null);

        $response->assertViewHas('accounts');
        $response->assertViewHas('months');

        $viewAccounts = $response->viewData('accounts');
        $this->assertEquals($viewAccounts->count(), $accounts->count());
    }

    /**
     * @group shouldRun
     */
    public function test_table1_data()
    {
        $account = factory(Account::class)->create();
        //TRANSACTIONS/SUMMARY FOR CURRENT MONTH
        $t1 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()]);
        $t2 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()]);
        $t3 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()]);
        $s1 = factory(MonthlySummary::class)->create(['account_id' => $account->id, 'month_date' => now()]);

        //TRANSACTIONS/SUMMARY FOR LAST MONTH
        $t4 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subMonth()]);
        $t5 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subMonth()]);
        $t6 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subMonth()]);
        $s2 = factory(MonthlySummary::class)->create(['account_id' => $account->id, 'month_date' => now()->subMonth()]);

        //TEST FOR CURRENT MONTH
        $user = User::find(1);
        $current_month = now()->format('Y-m');
        $response = $this->actingAs($user)
            ->get(route('account.transactions.summary', ['account_id' => $account->id, 'month' => $current_month]));

        $response->assertViewHas('table1');
        $table1 = $response->viewData('table1');

        $this->assertEquals(3, $table1->transactions->count());
        $this->assertEquals($table1->transactions->take(1)->last()->code, $t1->code);
        $this->assertEquals($table1->transactions->take(2)->last()->code, $t2->code);
        $this->assertEquals($table1->transactions->take(3)->last()->code, $t3->code);

        $this->assertEquals($table1->monthlySummary->id, $s1->id);
        $this->assertEquals($table1->monthlySummary->net_change, $s1->net_change);
        $this->assertEquals($table1->monthlySummary->ending_balance, $s1->ending_balance);
        $this->assertEquals($table1->monthlySummary->beginning_balance, $s1->beginning_balance);

        //TEST FOR MONTH BACK
        $user = User::find(1);
        $current_month = now()->subMonth()->format('Y-m');
        $response = $this->actingAs($user)
            ->get(route('account.transactions.summary', ['account_id' => $account->id, 'month' => $current_month]));

        $response->assertViewHas('table1');
        $table1 = $response->viewData('table1');

        $this->assertEquals(3, $table1->transactions->count());
        $this->assertEquals($table1->transactions->take(1)->last()->code, $t4->code);
        $this->assertEquals($table1->transactions->take(2)->last()->code, $t5->code);
        $this->assertEquals($table1->transactions->take(3)->last()->code, $t6->code);

        $this->assertEquals($table1->monthlySummary->id, $s2->id);
        $this->assertEquals($table1->monthlySummary->net_change, $s2->net_change);
        $this->assertEquals($table1->monthlySummary->ending_balance, $s2->ending_balance);
        $this->assertEquals($table1->monthlySummary->beginning_balance, $s2->beginning_balance);
    }
}
