<?php

namespace Phone\Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Phone\Models\PhoneNumber;
use Phone\DTO\TransactionGroup;
use Phone\Models\PhoneTransaction;
use Phone\DTO\TransactionListParameters;
use Phone\Repositories\TransactionListRepository;

class TransactionListRepositoryTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_getting_transaction_groups_with_default_settings()
    {
        $number1 = factory(PhoneNumber::class)->create();
        $transactions1 = factory(PhoneTransaction::class, 5)->create(['phone_number_id' => $number1->id]);

        $number2 = factory(PhoneNumber::class)->create();
        $transactions2 = factory(PhoneTransaction::class, 5)->create(['phone_number_id' => $number2->id]);

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([]);
        $repo->setParams($params);

        $groups = $repo->getTransactionListGroups();

        /** @var TransactionGroup $group1 */
        $group1 = $groups->where('groupKey', $number1->phone_number)->first();
        $this->assertTrue($group1->groupedBy === TransactionListParameters::GROUP_BY_NUMBER);
        $transactions = $group1->getTransactions();
        $this->assertTrue($transactions->count() === 5);

        foreach ($transactions as $key => $transaction) {

            //check if transactions are only from first phone number
            $this->assertNotNull($transactions1->where('id', $transaction->id)->first());

            //check if ordering is correct
            if (isset($transactions[$key + 1])) {
                $this->assertTrue($transaction->date->gte($transactions[$key + 1]->date));
            }
        }

        $group2 = $groups->where('groupKey', $number2->phone_number)->first();
        $this->assertTrue($group2->groupedBy === TransactionListParameters::GROUP_BY_NUMBER);
        $transactions = $group2->getTransactions();
        $this->assertTrue($transactions->count() === 5);

        foreach ($transactions as $key => $transaction) {

            //check if transactions are only from second phone number
            $this->assertNotNull($transactions2->where('id', $transaction->id)->first());

            //check if ordering is correct
            if (isset($transactions[$key + 1])) {
                $this->assertTrue($transaction->date->gte($transactions[$key + 1]->date));
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function test_getting_transaction_groups_groupped_by_date_and_ordering_in_asc()
    {
        $transactions1 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-01-01']);
        $transactions3 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-03-01']);
        $transactions4 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-04-01']);
        $transactions2 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-02-01']);
        $transactions5 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-05-01']);

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([
            'groupBy'        => TransactionListParameters::GROUP_BY_DATE,
            'orderDirection' => TransactionListParameters::ORDER_BY_ASC,
        ]);
        $repo->setParams($params);

        $groups = $repo->getTransactionListGroups();

        $this->assertTrue($groups[0]->groupKey === '2012-01-01');
        $this->assertTrue($groups[1]->groupKey === '2012-02-01');
        $this->assertTrue($groups[2]->groupKey === '2012-03-01');
        $this->assertTrue($groups[3]->groupKey === '2012-04-01');
        $this->assertTrue($groups[4]->groupKey === '2012-05-01');

        /** @var TransactionGroup $group1 */
        $group1 = $groups->where('groupKey', '2012-01-01')->first();
        $this->assertTrue($group1->groupedBy === TransactionListParameters::GROUP_BY_DATE);
        $transactions = $group1->getTransactions();
        $this->assertTrue($transactions->count() === 5);

        foreach ($transactions as $key => $transaction) {

            //check if transactions are only from first phone number
            $this->assertNotNull($transactions1->where('id', $transaction->id)->first());

            //check if ordering is asc
            if (isset($transactions[$key + 1])) {
                $this->assertTrue($transaction->date->lte($transactions[$key + 1]->date));
            }
        }

        $group2 = $groups->where('groupKey', '2012-02-01')->first();
        $this->assertTrue($group2->groupedBy === TransactionListParameters::GROUP_BY_DATE);
        $transactions = $group2->getTransactions();
        $this->assertTrue($transactions->count() === 5);

        foreach ($transactions as $key => $transaction) {

            //check if transactions are only from second phone number
            $this->assertNotNull($transactions2->where('id', $transaction->id)->first());

            //check if ordering is asc
            if (isset($transactions[$key + 1])) {
                $this->assertTrue($transaction->date->lte($transactions[$key + 1]->date));
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function test_filtering_by_number()
    {
        $number1 = factory(PhoneNumber::class)->create();
        factory(PhoneTransaction::class, 5)->create(['phone_number_id' => $number1->id]);
        $number2 = factory(PhoneNumber::class)->create();
        factory(PhoneTransaction::class, 5)->create(['phone_number_id' => $number2->id]);
        $number3 = factory(PhoneNumber::class)->create();
        factory(PhoneTransaction::class, 5)->create(['phone_number_id' => $number3->id]);

        $number = PhoneNumber::all()->random();

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([
            'numberFilter' => $number->phone_number,
            'groupBy'      => TransactionListParameters::GROUP_BY_DATE,
        ]);
        $repo->setParams($params);

        $groups = $repo->getTransactionListGroups();

        foreach ($groups as $group) {
            foreach ($group->getTransactions() as $transaction) {
                $this->assertTrue($transaction->phone_number_id === $number->id);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function test_filtering_by_date()
    {
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-01-01']);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-03-01']);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-04-01']);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-02-01']);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-05-01']);

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([
            'dateFilter' => ['2012-01-01', '2012-03-01'],
        ]);
        $repo->setParams($params);

        $groups = $repo->getTransactionListGroups();

        foreach ($groups as $group) {
            foreach ($group->getTransactions() as $transaction) {
                $this->assertTrue($transaction->date->isBetween(Carbon::parse('2012-01-01'), Carbon::parse('2012-03-01'), true));
                $this->assertFalse($transaction->date->isBetween(Carbon::parse('2012-04-01'), Carbon::parse('2012-05-01'), true));
            }
        }
    }
}
