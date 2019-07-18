<?php

namespace Phone\Tests\Unit;

use Tests\TestCase;
use Carbon\CarbonInterface;
use Phone\DTO\TransactionListParameters;

class TransactionListParametersTest extends TestCase
{
    public function test_setting_date_filter()
    {
        $params = new TransactionListParameters();

        $params->set('dateFilter', ['2019-01-12', '2019-01-13']);

        $this->assertTrue($params->dateFilter[0] instanceof CarbonInterface);

        try {
            $params->set('dateFilter', ['2019-01-14', '2019-01-13']);
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === 'date_from should be greater than date_to');
        }

        try {
            $params->set('dateFilter', ['2019-01-01']);
        } catch (\Exception $e) {
            $this->assertTrue($e->getMessage() === 'dateFilter must be array with 2 values (date_from, date_to)');
        }

        $params->set('dateFilter', null);
        $this->assertTrue($params->dateFilter === null);
    }

    public function test_setting_group_by()
    {
        $params = new TransactionListParameters();

        $params->set('groupBy', $params::GROUP_BY_NUMBER);
        $this->assertTrue($params->groupBy === $params::GROUP_BY_NUMBER);
        $this->assertTrue($params->groupByInverse === $params::GROUP_BY_DATE);

        $params->set('groupBy', $params::GROUP_BY_DATE);
        $this->assertTrue($params->groupBy === $params::GROUP_BY_DATE);
        $this->assertTrue($params->groupByInverse === $params::GROUP_BY_NUMBER);

        $params->set('groupBy', null);
        $this->assertTrue($params->groupBy === $params::GROUP_BY_NUMBER);
        $this->assertTrue($params->groupByInverse === $params::GROUP_BY_DATE);
    }

    public function test_get_date_filter_strings()
    {
        $params = new TransactionListParameters();

        $params->set('dateFilter', ['2019-01-12', '2019-01-13']);

        $strings = $params->getDateFilterStrings();

        $this->assertIsArray($strings);
        $this->assertCount(2, $strings);
        $this->assertTrue($strings[0] === '2019/01/12');
        $this->assertTrue($strings[1] === '2019/01/13');

        $params->set('dateFilter', null);
        $strings = $params->getDateFilterStrings();
        $this->assertTrue($strings === null);
    }
}
