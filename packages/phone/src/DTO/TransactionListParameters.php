<?php

namespace Phone\DTO;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class TransactionListParameters
{
    const GROUP_BY_NUMBER = 'phone_number';
    const GROUP_BY_DATE   = 'date';

    const ORDER_BY_DESC = 'desc';
    const ORDER_BY_ASC  = 'asc';

    const ORDER_BY_DATE = 'date';

    const DATE_FORMAT    = 'Y/m/d';
    const DATE_FORMAT_JS = 'YYYY/MM/DD';

    /** @var null|string */
    public $orderBy = self::ORDER_BY_DATE;

    /** @var null|string */
    public $orderDirection = self::ORDER_BY_DESC;

    /**
     * THIS SHOULD ALWAYS BE A 2 ELEMENT ARRAY WHERE BOTS ELEMENTS ARE CARBON INSTANCES
     * FIRST ONE IF DATE_FROM SECOND ONE IS DATE_TO.
     * @var null|array
     */
    public $dateFilter = null;

    /**
     * Phone number should go here (NOT phone number id).
     * @var null|string
     */
    public $numberFilter = null;

    /** @var null|string */
    public $groupBy = self::GROUP_BY_NUMBER;

    /** @var null|string */
    public $groupByInverse = self::GROUP_BY_DATE;

    /**
     * TransactionListParameters constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function set($key, $value): void
    {
        //TODO TEST THIS THROW
        if (!in_array($key, array_keys(get_class_vars(self::class)))) {
            throw new \Exception('This key does not exit in this object.');
        }

        if (method_exists($this, 'set'.ucfirst($key))) {
            call_user_func([$this, 'set'.ucfirst($key)], $value);

            return;
        }

        $this->{$key} = $value;
    }

    /**
     * @param string $value
     */
    public function setGroupBy(string $value = null)
    {
        if ($value === self::GROUP_BY_DATE) {
            $this->groupBy        = self::GROUP_BY_DATE;
            $this->groupByInverse = self::GROUP_BY_NUMBER;

            return;
        }
        $this->groupBy        = self::GROUP_BY_NUMBER;
        $this->groupByInverse = self::GROUP_BY_DATE;
    }

    /**
     * @param array $dates
     * @throws \Exception
     */
    public function setDateFilter(array $dates = null): void
    {
        if (is_null($dates)) {
            $this->dateFilter = null;

            return;
        }
        if (count($dates) !== 2) {
            throw new \Exception('dateFilter must be array with 2 values (date_from, date_to)');
        }

        $dates = array_values($dates);
        if (!$dates[0] instanceof CarbonInterface) {
            $dates[0] = Carbon::parse($dates[0]);
        }
        if (!$dates[1] instanceof CarbonInterface) {
            $dates[1] = Carbon::parse($dates[1]);
        }

        if ($dates[0]->gt($dates[1])) {
            throw new \Exception('date_from should be greater than date_to');
        }

        $this->dateFilter = $dates;
    }

    /**
     * @return array|null
     */
    public function getDateFilterStrings(): ?array
    {
        if (!$this->dateFilter) {
            return null;
        }

        return [$this->dateFilter[0]->format(self::DATE_FORMAT), $this->dateFilter[1]->format(self::DATE_FORMAT)];
    }
}
