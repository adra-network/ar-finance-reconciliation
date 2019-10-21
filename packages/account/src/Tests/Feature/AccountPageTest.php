<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Models\AccountImport;
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
        $response->assertViewHas('batchTable', null);

        $response->assertViewHas('accounts');

        $viewAccounts = $response->viewData('accounts');
        $this->assertEquals($viewAccounts->count(), $accounts->count());
    }

    /**
     * @group shouldRun
     */
    public function test_table1_data()
    {
        $account = factory(Account::class)->create();
        $accountImport = factory(AccountImport::class)->create();
        //TRANSACTIONS/SUMMARY FOR CURRENT MONTH
        $t1 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now(), 'account_import_id' => $accountImport->id]);
        $t2 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now(), 'account_import_id' => $accountImport->id]);
        $t3 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now(), 'account_import_id' => $accountImport->id]);
        $s1 = factory(MonthlySummary::class)->create(['account_id' => $account->id, 'month_date' => now(), 'account_import_id' => $accountImport->id]);

        //TRANSACTIONS/SUMMARY FOR LAST MONTH
        $accountImport2 = factory(AccountImport::class)->create();
        $t4 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subMonth(), 'account_import_id' => $accountImport2->id]);
        $t5 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subMonth(), 'account_import_id' => $accountImport2->id]);
        $t6 = factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->subMonth(), 'account_import_id' => $accountImport2->id]);
        $s2 = factory(MonthlySummary::class)->create(['account_id' => $account->id, 'month_date' => now()->subMonth(), 'account_import_id' => $accountImport2->id]);

        //TEST FOR CURRENT MONTH
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->get(route('account.transactions.summary', ['account_id' => $account->id, 'import' => $accountImport->id]));

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
        $response = $this->actingAs($user)
            ->get(route('account.transactions.summary', ['account_id' => $account->id, 'import' => $accountImport2->id]));

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
