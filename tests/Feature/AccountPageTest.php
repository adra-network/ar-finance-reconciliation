<?php

namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use App\User;
use Tests\TestCase;

class AccountPageTest extends TestCase
{

    /**
     * @group account
     */
    public function test_accounts_and_months_dropdowns_and_no_table()
    {
        $account = Account::create([
            'code' => 'account-111',
            'name' => 'account-1111-name'
        ]);

        $transaction1 = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now(),
            'code' => '123456',
            'debit_amount' => 100
        ]);
        $transaction2 = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->subDays(60),
            'code' => '12345678',
            'debit_amount' => 200
        ]);

        $user = User::find(1);
        $response = $this->actingAs($user)
            ->get('/admin/transactions/account');

        // Test if we have accounts dropdown with empty value (not sure it needs to be 0, maybe empty is better?)
        $response->assertSee('<option value="0">-- ' . trans('global.account.choose_account') . ' --</option>');

        // Test if we have both accounts ID and name in a dropdown of accounts
        $response->assertSee('<option value="'.$account->id.'"');
        $response->assertSee($account->name.'</option>');

        // Test if we have months dropdown with empty value
        $response->assertSee('<option value="">-- ' . trans('global.account.choose_month') . ' --</option>');

        // Dropdown of months should contain ONLY months that have at least one transaction, order by date desc
        // Test if we have transaction1 month in a dropdown of months
        // Dropdown value is different from option, to avoid / symbol in URL, so Y-m is better imho, but debatable
        $response->assertSee('<option value="'.$transaction1->transaction_date->format('Y-m').'"');
        $response->assertSee($transaction1->transaction_date->format('m/Y').'</option>');

        // Test if we have transaction2 month in a dropdown of months
        $response->assertSee('<option value="'.$transaction2->transaction_date->format('Y-m').'"');
        $response->assertSee($transaction2->transaction_date->format('m/Y').'</option>');

        // Test if we DON'T have older months in a dropdown of months
        $older_date = $transaction2->transaction_date->subDays(60);
        $response->assertDontSee('<option value="'.$older_date->format('Y-m').'"');
        $response->assertDontSee($older_date->format('m/Y').'</option>');

        // Test if we DON'T see the table of data yet, because we haven't chosen account and month yet
        $response->assertDontSee('<th>'.trans('global.fields.transaction_id').'</th>');

    }

    /**
     * @group account
     */
    public function test_data_by_account_and_month()
    {
        $account = Account::create([
            'code' => 'account-111',
            'name' => 'account-1111-name'
        ]);

        $transaction_today_debit = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now(),
            'code' => 'transaction-today-debit-01',
            'debit_amount' => 1000
        ]);
        $transaction_today_credit = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now(),
            'code' => 'transaction-today-credit-02',
            'credit_amount' => 2000
        ]);
        $transaction_past_year = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->subYear(), // just to make sure it's not same month
            'code' => 'transaction-past-year-03',
            'credit_amount' => 3000
        ]);

        $user = User::find(1);
        $current_month = now()->format('Y-m');
        $response = $this->actingAs($user)
            ->get('/admin/transactions/account?account_id=' . $account->id . '&month=' . $current_month);


        // Test if dropdown has SELECTED value of chosen account_id and month
        $response->assertSee('<option value="'.$account->id.'" selected');
        $response->assertSee('<option value="'.$current_month.'" selected');

        // Table should show data only from chosen month
        // Test if table shows both today's transactions but doesn't show past year's transaction
        $response->assertSee('<td>'.$transaction_today_debit->code.'</td>');
        $response->assertSee('<td>'.$transaction_today_credit->code.'</td>');
        $response->assertDontSee('<td>'.$transaction_past_year->code.'</td>');

        // Test if table shows debit and credit in correct columns with correct amounts
        // I suggest to add "fake" CSS class which wouldn't actually do anything
        $response->assertSee('<td class="td-debit">'.number_format($transaction_today_debit->debit_amount, 2).'</td>');
        $response->assertSee('<td class="td-credit">'.number_format($transaction_today_credit->credit_amount, 2).'</td>');

    }

}
