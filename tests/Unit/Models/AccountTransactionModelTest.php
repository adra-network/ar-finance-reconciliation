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
    public function test_getCreditOrDebit_method()
    {
        $account = factory(Account::class)->create();

        $transaction = factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 123, 'credit_amount' => 0]);
        $this->assertEquals($transaction->getCreditOrDebit(), 123);

        $transaction = factory(AccountTransaction::class)->create(['account_id' => $account->id, 'debit_amount' => 0, 'credit_amount' => 123]);
        $this->assertEquals($transaction->getCreditOrDebit(), -123);
    }

    /**
     * @group shouldRun
     */
    public function test_getReferenceId_method()
    {
        $account = factory(Account::class)->create();

        $references = [
            'TA1234 Testing',
            'TA1234AD Test Reference',
            'Test TA1234AD Reference',
            'Test TA1234 Reference',
            'Test Reference TA1234',
        ];

        foreach ($references as $reference) {
            /** @var AccountTransaction $transaction */
            $transaction = factory(AccountTransaction::class)->create([
                'account_id' => $account->id,
                'reference'  => $reference,
            ]);

            $this->assertEquals($transaction->getReferenceId(), 'TA1234');
        }

        $transaction = factory(AccountTransaction::class)->create([
            'account_id' => $account->id,
            'reference'  => 'TAasd',
        ]);
        $this->assertNull($transaction->getReferenceId(), 'TA1234');
    }

    /**
     * @group shouldRun
     */
    public function test_updateComment_method()
    {
        $mtc = 'my-test-comment';

        /** @var AccountTransaction $transaction */
        $transaction = factory(AccountTransaction::class)->create();

        $transaction->updateComment($mtc);

        //assert that the current model has updated comment
        $this->assertEquals($transaction->comment, $mtc);
        //assert that the comment was saved to the database
        $this->assertEquals(AccountTransaction::first()->comment, $mtc);
    }
}