<?php

namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use App\User;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_search_returns_correct_results()
    {
        $account = factory(Account::class)->create([
            'code' => 'account-123456',
            'name' => 'account-123456-name',
        ]);

        factory(AccountTransaction::class)->create([
            'account_id'       => $account->id,
            'transaction_date' => now()->format('m/d/Y'),
            'reference'        => 'reference-123',
            'journal'          => 'journal-123',
            'code'             => 'transaction-123456',
            'debit_amount'     => 12.34,
        ]);

        $user = User::find(1);

        // Searching for "123" query
        $response = $this->actingAs($user)
            ->get('/admin/search?search%5Bterm%5D=123&search%5B_type%5D=query');
        $results = json_decode($response->content())->results;

        // Test that we've found both account and transaction by "123" query
        $this->assertEquals(2, count($results));

        $this->assertEquals('account-123456-name', $results[0]->name);

        $this->assertEquals('reference-123', $results[1]->reference);
        $this->assertEquals('journal-123', $results[1]->journal);
    }

    /**
     * @group shouldRun
     */
    public function test_search_returns_empty_results()
    {
        $account = factory(Account::class)->create([
            'code' => 'account-123456',
            'name' => 'account-123456-name',
        ]);

        factory(AccountTransaction::class)->create([
            'account_id'       => $account->id,
            'transaction_date' => now()->format('m/d/Y'),
            'reference'        => 'reference-123',
            'journal'          => 'journal-123',
            'code'             => 'transaction-123456',
            'debit_amount'     => 12.34,
        ]);

        $user = User::find(1);

        // Searching for "789" query
        $response = $this->actingAs($user)
            ->get('/admin/search?search%5Bterm%5D=789&search%5B_type%5D=query');
        $results = json_decode($response->content())->results;

        // Test that there are no records by "789" query
        $this->assertEquals(0, count($results));
    }
}
