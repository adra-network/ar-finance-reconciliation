<?php

namespace Tests\Browser;

use Account\Models\Account;
use Account\Models\Transaction;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{

    public function testThatTransactionModalLoads()
    {
        $u = User::findOrFail(1);

        $account = factory(Account::class)->create(['user_id' => $u->id]);
        $transactions = factory(Transaction::class, 10)->create(['account_id' => $account]);

        $this->browse(function (Browser $browser) use ($u, $transactions) {
            $browser->loginAs($u)
                ->assertAuthenticatedAs($u)
                ->visitRoute('account.transactions.index')
                ->click('#tab1 .fa-cogs:first-child')
                ->waitFor('.modal-dialog');

            foreach ($transactions as $transaction) {
                $browser->assertSeeIn('.modal-dialog', $transaction->code);
            }

            $browser->press('Close')
                ->waitUntilMissing('.modal-backdrop')
                ->click('#ui-id-2')
                ->click('#tab2 .fa-cogs:first-child')
                ->waitFor('.modal-dialog');

            foreach ($transactions as $transaction) {
                $browser->assertSeeIn('.modal-dialog', $transaction->code);
            }
        });
    }
}
