<?php
namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use Tests\TestCase;

class AccountTransactionModelTest extends TestCase
{

    /**
     * @group shouldRun
     */
    public function test_get_credit_or_debit_method()
    {

        $account = factory(Account::class)->create();

        $transaction = factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]);
        $this->assertEquals($transaction->getCreditOrDebit(), 123);

        $transaction = factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 123]);
        $this->assertEquals($transaction->getCreditOrDebit(), -123);

    }
}