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

    /**
     * @group shouldRun
     */
    public function test_get_reference_id_method()
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
}
