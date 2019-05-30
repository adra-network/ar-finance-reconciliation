<?php

namespace Tests\Feature;

use App\Account;
use App\AccountMonthlySummary;
use App\AccountTransaction;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class AccountPageTest extends TestCase
{

    /**
     * @group shouldRun
     */
    public function test_accounts_and_months_dropdowns_and_no_table()
    {
        $account = Account::create([
            'code' => 'account-111',
            'name' => 'account-1111-name'
        ]);

        $transaction = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now(),
            'code' => '123456',
            'debit_amount' => 100
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

        // Test if we have transaction month in a dropdown of months
        // Dropdown value is different from option, to avoid / symbol in URL, so Y-m is better imho, but debatable
        $transaction_date = Carbon::createFromFormat('m/d/Y', $transaction->transaction_date);
        $response->assertSee('<option value="'.$transaction_date->format('Y-m').'"');
        $response->assertSee($transaction_date->format('m/Y').'</option>');

        // Test if we DON'T see the table of data yet, because we haven't chosen account and month yet
        $response->assertDontSee('<th>'.trans('global.fields.transaction_id').'</th>');

    }

    /**
     * @group shouldRun
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

    /**
     * @group shouldRun
     */
    public function test_monthly_summary_numbers_show()
    {
        $account = Account::create([
            'code' => 'account-111',
            'name' => 'account-1111-name'
        ]);

        $summary = AccountMonthlySummary::create([
            'account_id' => $account->id,
            'month_date' => now(),
            'beginning_balance' => 100,
            'net_change' => -200,
            'ending_balance' => -100,
        ]);

        $user = User::find(1);
        $current_month = now()->format('Y-m');
        $response = $this->actingAs($user)
            ->get('/admin/transactions/account?account_id=' . $account->id . '&month=' . $current_month);


        // Test if all the numbers are shown correctly
        $response->assertSee('<b>'.trans('global.account_page.beginning_balance').':</b> 100.00');
        $response->assertSee('<b>'.trans('global.account_page.net_change').':</b> -200.00');
        $response->assertSee('<b>'.trans('global.account_page.ending_balance').':</b> -100.00');

    }

}
