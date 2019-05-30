<?php

namespace Tests\Feature;

use App\Account;
use App\AccountTransaction;
use App\Repositories\AccountTransactionRepository;
use Tests\TestCase;

class AccountTransactionRepositoryTest extends TestCase
{

    /**
     * @group shouldRun
     */
    public function test_account_repository_get_suggested_transaction_groups()
    {
        $this->seed(\LocalSeeder::class);

        $account = Account::first();
        $groups = AccountTransactionRepository::getUnallocatedTransactionGroups();

        $this->assertNotNull($groups);
        $this->assertEquals($groups->count(), 4);

        $references = ['TA1234', 'TA12345', 'TA2345', 'TA23456'];
        foreach ($groups as $transactions) {
            $reference = array_shift($references);
            /** @var AccountTransaction $transaction */
            foreach ($transactions as $transaction) {
                $this->assertEquals($transaction->getReferenceId(), $reference);
            }
        }
    }

}