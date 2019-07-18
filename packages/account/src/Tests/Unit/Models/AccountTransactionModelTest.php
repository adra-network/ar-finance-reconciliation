<?php

namespace Tests\Feature;

use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;

class AccountTransactionModelTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getCreditOrDebit_method()
    {
        $account = factory(Account::class)->create();

        $transaction = factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => null]);
        $this->assertEquals($transaction->getCreditOrDebit(), 123);

        $transaction = factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => null, 'credit_amount' => 123]);
        $this->assertEquals($transaction->getCreditOrDebit(), -123);

        $transaction = factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => null]);
        $this->assertEquals($transaction->getCreditOrDebit(), 0);

        $transaction = factory(Transaction::class)->create(['account_id' => $account->id, 'debit_amount' => null, 'credit_amount' => 0]);
        $this->assertEquals($transaction->getCreditOrDebit(), 0);
    }

    /**
     * @group shouldRun
     */
    public function test_getReferenceId_method()
    {
        $account = factory(Account::class)->create();

        $transaction = factory(Transaction::class)->create([
            'account_id' => $account->id,
            'reference'  => 'TAasd',
        ]);
        $this->assertNull($transaction->getReferenceId()->toString(), 'TA1234');
    }

    /**
     * @group shouldRun
     */
    public function test_updateComment_method()
    {
        $mtc = 'my-test-comment';

        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create();

        $transaction->updateComment($mtc);

        //assert that the current model has updated comment
        $this->assertEquals($transaction->comment, $mtc);
        //assert that the comment was persisted to the database
        $this->assertEquals(Transaction::first()->comment, $mtc);

        $transaction->updateComment(null);

        //assert that the current model has updated comment
        $this->assertEquals($transaction->comment, null);
        //assert that the comment was persisted to the database
        $this->assertEquals(Transaction::first()->comment, null);
    }
}
