<?php

namespace Account\DTO;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class TransactionReferenceIdData
{
    /** @var string */
    public $reference;

    /** @var string|null */
    private $ta = null;

    /** @var Collection */
    private $months;

    /** @var Carbon|null */
    private $date = null;

    /** @var bool */
    private $isReversal = false;

    /**
     * TransactionReferenceIdData constructor.
     * @param string $reference
     */
    public function __construct(string $reference)
    {
        $this->months = new Collection([
            (object) ['short' => 'jan', 'full' => 'january', 'number' => 1],
            (object) ['short' => 'feb', 'full' => 'february', 'number' => 2],
            (object) ['short' => 'mar', 'full' => 'march', 'number' => 3],
            (object) ['short' => 'apr', 'full' => 'april', 'number' => 4],
            (object) ['short' => 'may', 'full' => 'may', 'number' => 5],
            (object) ['short' => 'jun', 'full' => 'june', 'number' => 6],
            (object) ['short' => 'jul', 'full' => 'july', 'number' => 7],
            (object) ['short' => 'aug', 'full' => 'august', 'number' => 8],
            (object) ['short' => 'sep', 'full' => 'september', 'number' => 9],
            (object) ['short' => 'oct', 'full' => 'october', 'number' => 10],
            (object) ['short' => 'nov', 'full' => 'november', 'number' => 11],
            (object) ['short' => 'dec', 'full' => 'december', 'number' => 12],
        ]);

        $this->reference = $reference;

        //MATCH TA REFERENCES
        if (preg_match('/(TA[0-9]+)/', $this->reference, $matches)) {
            $this->ta = $matches[0];
        }

        //MATCH REVERSE REFERENCES
        if (preg_match('/(\<reverse\>)|(\<reversal\>)/i', $this->reference, $matches)) {
            $this->isReversal = true;
        }

        //MATCH DATE REFERENCES
        $ref = str_replace($this->months->pluck('full')->toArray(), $this->months->pluck('short')->toArray(), Str::lower($this->reference));
        $regex = sprintf("/((%s)\s?(?:\'\d\d)?)\s?CC/i", $this->months->implode('short', '|'));
        if (preg_match($regex, $ref, $matches)) {

            //format date for extracting month and year
            $date = trim(
                str_replace(
                    "'",
                    ' ',
                    str_replace(
                        " '",
                        "'",
                        $matches[1]
                    )
                )
            );

            //month - year will be separated with a blank space
            $date = explode(' ', $date);

            //month will always be first
            $month = $date[0];

            //year can be second or not exist at all
            if (isset($date[1])) {
                $date = Carbon::create('20'.$date[1]);
            } else {
                $date = now()->startOfMonth();
            }

            $date->setMonth($this->months->where('short', $month)->first()->number);

            $this->date = $date;
        }
    }

    /**
     * @return string
     */
    public function getTA(): ?string
    {
        return $this->ta;
    }

    /**
     * @return bool
     */
    public function isReversal(): bool
    {
        return $this->isReversal;
    }

    /**
     * @return Carbon|null
     */
    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getDateString(): ?string
    {
        return optional($this->getDate(), function (Carbon $date) {
            return $date->format(TransactionReconciliationGroupData::DATE_FORMAT);
        });
    }

    /**
     * @param string|null $reference
     * @return TransactionReferenceIdData|null
     */
    public static function make(string $reference = null): ?self
    {
        if (is_null($reference)) {
            return null;
        }

        return new self($reference);
    }
}
