<?php

namespace Phone\Tests\Unit;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Phone\DTO\TransactionGroup;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;
use Phone\DTO\TransactionListParameters;
use Phone\Repositories\TransactionListRepository;

class TransactionListRepositoryTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_getting_transaction_groups_with_default_settings()
    {
        $accountNumber = factory(AccountPhoneNumber::class)->create();

        $number1 = factory(CallerPhoneNumber::class)->create();
        $transactions1 = factory(PhoneTransaction::class, 5)->create(['caller_phone_number_id' => $number1->id, 'total_charges' => 1, 'account_phone_number_id' => $accountNumber->id]);

        $number2 = factory(CallerPhoneNumber::class)->create();
        $transactions2 = factory(PhoneTransaction::class, 5)->create(['caller_phone_number_id' => $number2->id, 'total_charges' => 1, 'account_phone_number_id' => $accountNumber->id]);

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([
            'numberFilter' => $accountNumber->phone_number,
        ]);
        $repo->setParams($params);
        $repo->setUser(User::find(1));

        $groups = $repo->getTransactionListGroups();

        /** @var TransactionGroup $group1 */
        $group1 = $groups->where('groupKey', $number1->phone_number)->first();
        $this->assertNotNull($group1);
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
        $accountPhoneNumber = factory(AccountPhoneNumber::class)->create();

        $transactions1 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-01-01', 'total_charges' => 1, 'account_phone_number_id' => $accountPhoneNumber->id]);
        $transactions3 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-03-01', 'total_charges' => 1, 'account_phone_number_id' => $accountPhoneNumber->id]);
        $transactions4 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-04-01', 'total_charges' => 1, 'account_phone_number_id' => $accountPhoneNumber->id]);
        $transactions2 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-02-01', 'total_charges' => 1, 'account_phone_number_id' => $accountPhoneNumber->id]);
        $transactions5 = factory(PhoneTransaction::class, 5)->create(['date' => '2012-05-01', 'total_charges' => 1, 'account_phone_number_id' => $accountPhoneNumber->id]);

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([
            'groupBy' => TransactionListParameters::GROUP_BY_DATE,
            'orderDirection' => TransactionListParameters::ORDER_BY_ASC,
            'numberFilter' => $accountPhoneNumber->phone_number,
        ]);
        $repo->setParams($params);
        $repo->setUser(User::find(1));

        $groups = $repo->getTransactionListGroups();

        $this->assertTrue($groups[0]->groupKey === '01/01/2012');
        $this->assertTrue($groups[1]->groupKey === '02/01/2012');
        $this->assertTrue($groups[2]->groupKey === '03/01/2012');
        $this->assertTrue($groups[3]->groupKey === '04/01/2012');
        $this->assertTrue($groups[4]->groupKey === '05/01/2012');

        /** @var TransactionGroup $group1 */
        $group1 = $groups->where('groupKey', '01/01/2012')->first();
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

        $group2 = $groups->where('groupKey', '02/01/2012')->first();
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
    public function test_filtering_by_date()
    {
        $accountPhoneNumber = factory(AccountPhoneNumber::class)->create();

        factory(PhoneTransaction::class, 5)->create(['date' => '2012-01-01', 'account_phone_number_id' => $accountPhoneNumber->id]);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-03-01', 'account_phone_number_id' => $accountPhoneNumber->id]);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-04-01', 'account_phone_number_id' => $accountPhoneNumber->id]);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-02-01', 'account_phone_number_id' => $accountPhoneNumber->id]);
        factory(PhoneTransaction::class, 5)->create(['date' => '2012-05-01', 'account_phone_number_id' => $accountPhoneNumber->id]);

        $repo = new TransactionListRepository();
        $params = new TransactionListParameters([
            'dateFilter' => ['2012-01-01', '2012-03-01'],
            'numberFilter' => $accountPhoneNumber->phone_number,
        ]);
        $repo->setUser(User::find(1));
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
