<?php

namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use App\Reconciliation;
use App\User;
use Tests\TestCase;

class BatchPageTest extends TestCase
{

    /**
     * @group
     */
    public function test_batch_page_see_one_record()
    {
        $user = User::find(1);

        $account = Account::create([
            'code' => 'account-123456',
            'name' => 'account-123456-name'
        ]);

        AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->format('m/d/Y'),
            'code' => 'transaction-123456',
            'debit_amount' => 12.34,
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/transactions');

        $response->assertSee('account-123456-name');
        $response->assertSee('transaction-123456');
        $response->assertSee('12.34');
    }


    /**
     * @group
     */
    public function test_checkbox_show_previous_reconciliations_visible()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->get('/admin/transactions');

        // Test if checkbox actually is shown
        $response->assertSee('<input type="checkbox" name="show_previous"');
        $response->assertSee(trans('global.transaction.show_previous_reconciliations'));
    }

    /**
     * @group
     */
    public function test_show_previous_filter()
    {
        // Creating account
        $account = Account::create([
            'code' => 'account-123456',
            'name' => 'account-123456-name'
        ]);

        // Creating debit+credit transactions from 1 month ago, and reconcile them in the past, 1 month ago
        $transaction_month_debit = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->subMonth()->format('m/d/Y'),
            'code' => 'transaction-debit-123',
            'debit_amount' => 12.34,
        ]);
        $transaction_month_credit = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->subMonth()->format('m/d/Y'),
            'code' => 'transaction-credit-456',
            'credit_amount' => 12.34,
        ]);
        $reconciliation = Reconciliation::create([
            'account_id' => $account->id,
            'is_fully_reconciled' => 1,
            'created_at' => now()->subMonth()
        ]);
        $transaction_month_credit->update(['reconciliation_id' => $reconciliation->id]);
        $transaction_month_debit->update(['reconciliation_id' => $reconciliation->id]);

        // Creating debit+credit transactions from 1 year ago, and reconcile them in the past, 1 year ago
        $transaction_year_debit = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->subYear()->format('m/d/Y'),
            'code' => 'transaction-year-debit-123',
            'debit_amount' => 12.34,
        ]);
        $transaction_year_credit = AccountTransaction::create([
            'account_id' => $account->id,
            'transaction_date' => now()->subYear()->format('m/d/Y'),
            'code' => 'transaction-year-credit-456',
            'credit_amount' => 12.34,
        ]);
        $year_reconciliation = Reconciliation::create([
            'account_id' => $account->id,
            'is_fully_reconciled' => 1,
            'created_at' => now()->subYear()
        ]);
        $transaction_year_debit->update(['reconciliation_id' => $year_reconciliation->id]);
        $transaction_year_credit->update(['reconciliation_id' => $year_reconciliation->id]);


        $user = User::find(1);

        // Test that default view doesn't show the past reconciliation
        $response = $this->actingAs($user)
            ->get('/admin/transactions');
        $response->assertDontSee('transaction-debit-123');

        // Test that "show previous" view takes reconciliations from 2 months in the past and shows them
        $response = $this->actingAs($user)
            ->get('/admin/transactions?withPreviousMonths=2');
        $response->assertSee('transaction-debit-123');

        // Test that "show previous" view does NOT take reconciliations from 1 year ago in the past and does NOT show them
        $response = $this->actingAs($user)
            ->get('/admin/transactions?withPreviousMonths=2');
        $response->assertSee('transaction-year-debit-123');
    }

}
