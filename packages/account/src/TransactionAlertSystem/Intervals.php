<?php

namespace Account\TransactionAlertSystem;

use Account\Models\Transaction;
use Illuminate\Support\Collection;
use Account\TransactionAlertSystem\Notifications\TransactionsLate45Days;
use Account\TransactionAlertSystem\Notifications\TransactionsLate80Days;
use Account\TransactionAlertSystem\Notifications\TransactionsLate90Days;

class Intervals
{
    /** @var Collection */
    private $intervals;

    /**
     * Intervals constructor.
     */
    public function __construct()
    {
        $this->intervals = collect([
            new Interval([
                'min' => 45, //45 days in seconds
                'max' => 80, //80 days in seconds
                'alertClass' => 'alert-warning',
                'emailClass' => TransactionsLate45Days::class,
                'frequency' => Interval::FREQUENCY_EVERY_MONDAY,
                'pdfText' => 'Transaction over 45 days',
                'stars' => '*',
            ]),
            new Interval([
                'min' => 80, //80 days in seconds
                'max' => 90, //90 days in seconds
                'alertClass' => 'alert-danger',
                'emailClass' => TransactionsLate80Days::class,
                'frequency' => Interval::FREQUENCY_DAILY,
                'pdfText' => 'Transaction over 80 days',
                'stars' => '**',
            ]),
            new Interval([
                'min' => 90, //90 days in seconds
                'max' => null, //infinite
                'alertClass' => 'alert-danger',
                'emailClass' => TransactionsLate90Days::class,
                'frequency' => Interval::FREQUENCY_DAILY,
                'pdfText' => 'Transaction over 90 days and impacting your taxable income',
                'stars' => '***',
            ]),
        ]);
    }

    /**
     * @param Transaction $transaction
     * @return Interval|null
     */
    public function getIntervalByTransaction(Transaction $transaction): ?Interval
    {
        /** @var Interval $interval */
        foreach ($this->intervals as $interval) {
            if ($interval->min) {
                if (! $transaction->getTransactionDate()->lte($interval->getMinInCarbon())) {
                    continue;
                }
            }
            if ($interval->max) {
                if (! $transaction->getTransactionDate()->gt($interval->getMaxInCarbon())) {
                    continue;
                }
            }

            return $interval;
        }

        return null;
    }

    /**
     * @param int|null $index
     * @return Collection|Interval
     */
    public function getIntervals(int $index = null)
    {
        if ($index !== null) {
            return $this->intervals[$index];
        }

        return $this->intervals;
    }
}
