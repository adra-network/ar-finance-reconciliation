<?php

namespace Tests\Feature;

use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\Models\AccountImport;
use Account\Models\MonthlySummary;
use Account\Services\ReconciliationService;
use Account\Services\AccountPageTableService;

class AccountPageTableServiceTest extends TestCase
{
    /**
     * @group shouldRun
     */
    public function test_getTable1_method()
    {
        $account = factory(Account::class)->create();

        $accountImport = factory(AccountImport::class)->create();

        //in bounds
        factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->startOfMonth()->format('Y-m-d'), 'account_import_id' => $accountImport->id]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->endOfMonth()->format('Y-m-d'), 'account_import_id' => $accountImport->id]);

        //out of bounds
        factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->startOfMonth()->subSecond()->format('Y-m-d')]);
        factory(Transaction::class)->create(['account_id' => $account->id, 'transaction_date' => now()->endOfMonth()->addSecond()->format('Y-m-d')]);

        factory(MonthlySummary::class)->create(['account_id' => $account->id, 'account_import_id' => $accountImport->id]);

        $s = new AccountPageTableService($account, $accountImport);
        $table1 = $s->getTable1();

        $this->assertTrue(isset($table1->transactions));
        $this->assertTrue(isset($table1->monthlySummary));

        $this->assertEquals(2, $table1->transactions->count());
        $this->assertEquals(1, $table1->transactions->where('id', 1)->count());
        $this->assertEquals(1, $table1->transactions->where('id', 2)->count());

        $this->assertEquals($table1->transactions->where('id', 3)->count(), 0);
        $this->assertEquals($table1->transactions->where('id', 4)->count(), 0);
    }
}
