<?php

namespace Account\Tests\Unit\DTO;

use Carbon\Carbon;
use Tests\TestCase;
use Account\Models\Account;
use Account\Models\Transaction;
use Account\DTO\TransactionReferenceIdData;
use Account\DTO\TransactionReconciliationGroupData;

class TransactionReferenceIdDataTest extends TestCase
{
    public function test_get_ta_method()
    {
        $references = collect([
            (object) [
                'ref' => 'Jan CC: Barczykowski TA14945 Familly Allowance Jordan',
                'ta' => 'TA14945',
                'date' => Carbon::parse('2019-01'),
                'reversal' => false,
            ],
            (object) [
                'ref' => 'Mar CC: Barczykowski TA14945 Familly Allowance Jordan',
                'ta' => 'TA14945',
                'date' => Carbon::parse('2019-03'),
                'reversal' => false,
            ],
            (object) [
                'ref' => 'Personal portion: Barczykowski TA14945 Familly Allowance Jordan',
                'ta' => 'TA14945',
                'date' => null,
                'reversal' => false,
            ],
            (object) [
                'ref' => "Jan CC: Barczykowski TA14945 Feb-March'19 Jordan AAC Trip Expenses",
                'ta' => 'TA14945',
                'date' => Carbon::parse('2019-01'),
                'reversal' => false,
            ],
            (object) [
                'ref' => "Feb CC: Barczykowski TA1494 Feb-March'19 Jordan AAC Trip Expenses",
                'ta' => 'TA1494',
                'date' => Carbon::parse('2019-02'),
                'reversal' => false,
            ],
            (object) [
                'ref' => "Barczykowski TA1494 Feb-March'19 Jordan AAC Trip Expenses",
                'ta' => 'TA1494',
                'date' => null,
                'reversal' => false,
            ],
            (object) [
                'ref' => "<Reversal> Barczykowski Feb'19 CC Statement Expenses",
                'ta' => null,
                'date' => Carbon::parse('2019-02'),
                'reversal' => true,
            ],
            (object) [
                'ref' => "<Reversal> Barczykowski Jan'19 CC Statement Expenses",
                'ta' => null,
                'date' => Carbon::parse('2019-01'),
                'reversal' => true,
            ],
        ]);

        foreach ($references as $reference) {
            $data = TransactionReferenceIdData::make($reference->ref);

            $this->assertEquals($reference->ta, $data->getTA());

            if ($reference->date instanceof Carbon) {
                $this->assertTrue($data->getDate() instanceof Carbon);
                $this->assertTrue($data->getDate()->isSameAs($reference->date));
            } else {
                $this->assertEquals($reference->date, $data->getDate());
            }

            $this->assertEquals($reference->date, $data->getDate());
            $this->assertEquals($reference->reversal, $data->isReversal());
        }

        /** @var Account $account */
        $account = factory(Account::class)->create();
        foreach ($references as $reference) {
            factory(Transaction::class)->create([
                'account_id' => $account->id,
                'reference' => $reference->ref,
            ]);
        }

        $account->load('transactions');

        $groups = $account->getUnallocatedTransactionGroups();

        $this->assertNotNull(($groups->where('referenceString', Carbon::create('2019-01')->format(TransactionReconciliationGroupData::DATE_FORMAT))->first()));
        $this->assertNotNull($groups->where('referenceString', Carbon::create('2019-02')->format(TransactionReconciliationGroupData::DATE_FORMAT))->first());
    }
}
