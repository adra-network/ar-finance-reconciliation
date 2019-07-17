<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class HeaderMenuTest extends TestCase
{
    public function test_header_menu_items()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get(route('account.transactions.index'));

        $response->assertSeeInOrder(['Account-Reconciliation', 'Phone-Reconciliation', 'CCC-Reconciliation']);
    }

    public function test_phone_transactions_page()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get(route('phone.transactions.index'));

        $response->assertSuccessful();
    }

    public function test_ccc_transactions_page()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get(route('card.transactions.index'));

        $response->assertSuccessful();
        $response->assertSee('Coming soon');
    }

    public function test_ccc_sidebar()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get(route('card.transactions.index'));

        $response->assertDontSee('Batch');
    }

    public function test_phone_sidebar()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->get(route('phone.transactions.index'));

        $response->assertDontSee('Batch');
    }
}
