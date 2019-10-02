<?php

namespace Account\TransactionAlertSystem;

use Carbon\CarbonInterface;

class Interval
{
    /** @var string */
    const FREQUENCY_EVERY_MONDAY = 'monday';

    /** @var string */
    const FREQUENCY_DAILY = 'daily';

    /**
     * @var int
     * value in seconds
     */
    public $min;
    /**
     * @var int
     * value in seconds
     */
    public $max;

    /** @var string */
    public $alertClass;

    /** @var string */
    protected $emailClass;

    /** @var string */
    public $frequency = self::FREQUENCY_DAILY;

    /**
     * Interval constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return string
     */
    public function getEmailClass(): string
    {
        return $this->emailClass;
    }

    /**
     * @return CarbonInterface
     */
    public function getMinInCarbon(): CarbonInterface
    {
        return now()->subDays($this->min)->startOfDay();
    }

    /**
     * @return CarbonInterface
     */
    public function getMaxInCarbon(): CarbonInterface
    {
        return now()->subDays($this->max)->startOfDay();
    }

    /**
     * @param Interval $interval
     * @return bool
     */
    public function isInterval(self $interval)
    {
        return $this->max === $interval->max && $this->min === $interval->min;
    }
}
