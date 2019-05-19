<?php

namespace Tests\Feature;

use App\Account;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BatchPageTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_batch_page_see_one_record()
    {
        $user = User::find(1);

        $account = Account::create([
            'code' => 'account-123456',
            'name' => 'account-123456-name'
        ]);

        Transaction::create([
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

}
